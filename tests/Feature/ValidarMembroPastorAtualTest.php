<?php

namespace Tests\Feature;

use App\Services\ServiceBase\GetBaseParamsService;
use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class ValidarMembroPastorAtualTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-09 12:00:00'));
        $base = Mockery::mock(GetBaseParamsService::class);
        $base->shouldReceive('execute')->andReturn((object) []);
        $this->app->instance(GetBaseParamsService::class, $base);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_validation_uses_current_appointment_in_members_church(): void
    {
        $this->consulta((object) [
            'id' => 'membro-teste', 'nome' => 'Membro Teste', 'status' => 'A',
            'igreja_nome' => 'Igreja Teste', 'pastor_nome' => 'Pastor Atual',
        ]);

        $this->get(route('validar-membro.show', ['membro' => 'membro-teste']))
            ->assertOk()->assertSeeText('Pastor Atual')->assertSeeText('Igreja Teste')
            ->assertSeeText('Ativo')->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_no_current_appointment_displays_not_informed(): void
    {
        $this->consulta((object) [
            'id' => 'membro-teste', 'nome' => 'Membro Teste', 'status' => 'I',
            'igreja_nome' => 'Igreja Teste', 'pastor_nome' => null,
        ]);

        $this->get(route('validar-membro.show', ['membro' => 'membro-teste']))
            ->assertOk()->assertSeeText('Não informado')->assertSeeText('Inativo');
    }

    public function test_titular_can_be_minister_without_pastor_category_filter(): void
    {
        $this->consulta((object) [
            'id' => 'membro-teste', 'nome' => 'Membro Teste', 'status' => 'A',
            'igreja_nome' => 'Igreja Teste', 'pastor_nome' => 'Ministro Titular',
        ]);

        $this->get(route('validar-membro.show', ['membro' => 'membro-teste']))
            ->assertOk()->assertSeeText('Ministro Titular');
    }

    public function test_unknown_member_keeps_validation_page_available(): void
    {
        $this->consulta(null);
        $this->get(route('validar-membro.show', ['membro' => 'membro-teste']))->assertOk();
    }

    public function test_missing_member_does_not_query_database(): void
    {
        DB::shouldReceive('table')->never();
        $this->get(route('validar-membro.show'))->assertOk();
    }

    private function consulta(?object $membro): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('`pn`.`instituicao_id` = `igreja`.`id`', $sql);
            $this->assertStringContainsString('`igreja`.`id` = `mm`.`igreja_id`', $sql);
            $this->assertStringContainsString('`pf`.`titular` = ?', $sql);
            $this->assertStringContainsString('`pastor`.`status_id` = ?', $sql);
            $this->assertStringContainsString('date(`pn`.`data_nomeacao`) <= ?', $sql);
            $this->assertStringContainsString('(`pn`.`data_termino` is null or date(`pn`.`data_termino`) >= ?)', $sql);
            $this->assertStringContainsString('`pn`.`deleted_at` is null', $sql);
            $this->assertStringContainsString('`pastor`.`deleted_at` is null', $sql);
            $this->assertStringContainsString('order by `pn`.`data_nomeacao` desc, `pn`.`id` desc limit 1', $sql);
            $this->assertStringNotContainsString('categoria', $sql);
            $this->assertStringNotContainsString('clerigo_id', $sql);
            $this->assertStringNotContainsString('rolpermanente', $sql);
            $this->assertStringNotContainsString('pf.ordem', $sql);
            $this->assertStringNotContainsString('MAX(', $sql);
            $this->assertStringNotContainsString('igreja.pastor', $sql);
            $this->assertSame([1, 1, '2026-11-09', '2026-11-09', '', 'membro-teste'], $bindings);

            return true;
        })->andReturn($membro ? [$membro] : []);
    }
}
