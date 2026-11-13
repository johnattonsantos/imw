<?php

namespace Tests\Unit;

use App\Http\Controllers\IrrfPorIgrejaController;
use App\Services\ServiceContabilidade\IrrfPorIgrejaService;
use App\Services\ServiceBase\GetBaseParamsService;
use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class IrrfPorIgrejaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-13 12:00:00'));
        $regiao = (new \App\Models\InstituicoesInstituicao)->setRawAttributes(['id' => 8, 'nome' => 'Regiao Teste']);
        session(['session_perfil' => (object) ['instituicoes' => (object) ['regiao' => $regiao]]]);
        $base = Mockery::mock(GetBaseParamsService::class);
        $base->shouldReceive('execute')->andReturn((object) []);
        $this->app->instance(GetBaseParamsService::class, $base);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_query_includes_unidentified_clergy_and_limits_entries_and_region(): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('left join `pessoas_pessoas` as `clerigo`', $sql);
            $this->assertStringContainsString('`fl`.`deleted_at` is null', $sql);
            $this->assertStringContainsString('`fl`.`conciliado` = ?', $sql);
            $this->assertStringContainsString('`fl`.`data_movimento` between ? and ?', $sql);
            $this->assertStringContainsString('`fl`.`plano_conta_id` in (?, ?)', $sql);
            $this->assertStringContainsString('group by `fl`.`instituicao_id`', $sql);
            $this->assertStringContainsString('`irrf`.`instituicao_id` = `igreja`.`id`', $sql);
            $this->assertStringContainsString('`distrito`.`instituicao_pai_id` = ?', $sql);
            $this->assertStringContainsString('fl.plano_conta_id = 220 AND clerigo.id IS NULL', $sql);
            $this->assertStringContainsString('fl.plano_conta_id = 41 AND clerigo.id IS NULL', $sql);
            $this->assertStringContainsString('COUNT(CASE WHEN clerigo.id IS NULL THEN fl.id END)', $sql);
            $this->assertStringNotContainsString('pessoas_nomeacoes', $sql);
            $this->assertStringNotContainsString('clerigo.deleted_at', $sql);
            $this->assertSame([1, '2026-11-01', '2026-11-13', 41, 220, 8, 2, 1], $bindings);
            return true;
        })->andReturn([]);
        $this->assertCount(0, (new IrrfPorIgrejaService)->listar(8, '2026-11-01', '2026-11-13'));
    }

    public function test_controller_uses_logged_region_and_sums_report_columns(): void
    {
        $service = Mockery::mock(IrrfPorIgrejaService::class);
        $service->shouldReceive('listar')->once()->with(8, '2026-11-01', '2026-11-13')->andReturn(collect([
            (object) ['retido' => '100.00', 'repassado' => '70.00', 'diferenca' => '30.00',
                'retido_sem_clerigo' => '20.00', 'repassado_sem_clerigo' => '10.00', 'quantidade_sem_clerigo' => 2],
            (object) ['retido' => '200.00', 'repassado' => '250.00', 'diferenca' => '-50.00',
                'retido_sem_clerigo' => '0.00', 'repassado_sem_clerigo' => '50.00', 'quantidade_sem_clerigo' => 1],
        ]));
        $view = (new IrrfPorIgrejaController)->index(Request::create('/', 'GET', ['regiao_id' => 99]), $service);
        $this->assertSame('contabilidade.irrf.por-igreja', $view->name());
        $this->assertEquals(['retido' => 300, 'repassado' => 320, 'diferenca' => -20,
            'retido_sem_clerigo' => 20, 'repassado_sem_clerigo' => 60, 'quantidade_sem_clerigo' => 3], $view->getData()['totais']);
    }

    /** @dataProvider invalidPeriods */
    public function test_invalid_period_does_not_query_report(array $period): void
    {
        $service = Mockery::mock(IrrfPorIgrejaService::class);
        $service->shouldNotReceive('listar');
        $this->expectException(ValidationException::class);
        (new IrrfPorIgrejaController)->index(Request::create('/', 'GET', $period), $service);
    }

    public function invalidPeriods(): array
    {
        return [
            [['data_inicial' => '2026-11-20', 'data_final' => '2026-11-01']],
            [['data_inicial' => 'invalid']],
            [['data_final' => '2026-02-30']],
        ];
    }

    public function test_route_requires_financial_permission(): void
    {
        $route = app('router')->getRoutes()->getByName('regiao.relatorio.irrf-por-igreja');
        $this->assertSame('regiao/relatorio/irrf-por-igreja', $route->uri());
        $this->assertContains('seguranca:regiao-menu-relatorio-financeiro', $route->gatherMiddleware());
        $this->assertContains('seguranca:regiao-menu-relatorio', $route->gatherMiddleware());
    }

    public function test_financial_menu_has_a_shortcut_with_regional_financial_permissions(): void
    {
        $menu = file_get_contents(resource_path('views/template/menu-sidebar.blade.php'));
        $start = strpos($menu, 'href="#financeiro"');
        $end = strpos($menu, '</ul>', $start);
        $financialMenu = substr($menu, $start, $end - $start);
        $this->assertStringContainsString("route('regiao.relatorio.irrf-por-igreja')", $financialMenu);
        $this->assertStringContainsString("hasPerfilRegra('regiao-menu-relatorio') && auth()->user()->hasPerfilRegra('regiao-menu-relatorio-financeiro')", $financialMenu);
        $this->assertStringContainsString("Request::is('financeiro/*', 'regiao/relatorio/irrf-por-igreja')", $financialMenu);
    }
}
