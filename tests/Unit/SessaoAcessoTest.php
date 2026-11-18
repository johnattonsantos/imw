<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\ServiceRegiaoRelatorios\GestaoAcessosService;
use App\Services\ServiceRegiaoRelatorios\SessaoAcessoService;
use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class SessaoAcessoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-18 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function request(): Request
    {
        $request = Request::create('/dashboard');
        $session = new Store('test', new ArraySessionHandler(120));
        $session->start();
        $session->put('session_perfil', (object) ['perfil_id' => 7, 'instituicao_id' => 10]);
        $request->setLaravelSession($session);
        $request->setUserResolver(fn () => (new User)->forceFill(['id' => 5, 'name' => 'Usuario Teste']));
        return $request;
    }

    private function connection()
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        DB::shouldReceive('query')->andReturnUsing(fn () => $connection->query());
        return $connection;
    }

    public function test_login_and_logout_are_isolated_between_two_devices(): void
    {
        $connection = $this->connection();
        $connection->shouldReceive('affectingStatement')->times(3)->andReturn(1);
        $a = $this->request();
        $b = $this->request();
        $service = new SessaoAcessoService;
        $first = $service->iniciar($a);
        $second = $service->iniciar($b);
        $this->assertNotSame($first['id'], $second['id']);
        $this->assertNotSame($a->session()->getId(), $first['id']);
        $this->assertSame('2026-11-18 10:00:00', $first['login_em']);
        $connection->shouldReceive('update')->once()->withArgs(function ($sql, $bindings) use ($first, $second) {
            $this->assertStringContainsString('where `id` = ? and `user_id` = ? and `logout_em` is null', $sql);
            $this->assertSame($first['id'], $bindings[1]);
            $this->assertSame(5, $bindings[2]);
            $this->assertNotContains($second['id'], $bindings);
            return true;
        })->andReturn(1);
        $this->assertSame($first['id'], $service->finalizar($a));
    }

    public function test_profile_switches_share_login_but_have_distinct_profile_rows(): void
    {
        $connection = $this->connection();
        $inserts = [];
        $connection->shouldReceive('affectingStatement')->times(5)->andReturnUsing(function ($sql, $bindings) use (&$inserts) {
            preg_match('/insert ignore into `([^`]+)` \(([^)]+)\)/', $sql, $match);
            preg_match_all('/`([^`]+)`/', $match[2], $columns);
            $inserts[$match[1]][] = array_combine($columns[1], $bindings);
            return 1;
        });
        $connection->shouldReceive('update')->twice()->andReturn(1);
        $request = $this->request();
        $service = new SessaoAcessoService;
        $sessao = $service->iniciar($request);
        Carbon::setTestNow('2026-11-18 10:15:00');
        $vinculo = (object) ['regiao_id' => 8, 'perfil_nome' => 'Pastor', 'instituicao_nome' => 'Igreja Teste'];
        $service->registrarPerfil($request, $vinculo);
        $request->session()->put('session_perfil', (object) ['perfil_id' => 2, 'instituicao_id' => 20]);
        $vinculo->perfil_nome = 'Administrador Distrito';
        $service->registrarPerfil($request, $vinculo);
        $rows = $inserts['gestao_acessos_sessao_perfis'];
        $this->assertCount(2, $rows);
        $this->assertSame([$sessao['id'], $sessao['id']], array_column($rows, 'sessao_id'));
        $this->assertSame([7, 2], array_column($rows, 'perfil_id'));
        foreach ($inserts['gestao_acessos_sessoes'] as $row) {
            $this->assertSame('2026-11-18 10:00:00', $row['login_em']);
        }
    }

    public function test_existing_session_does_not_fabricate_login_time(): void
    {
        $connection = $this->connection();
        $connection->shouldReceive('affectingStatement')->twice()->withArgs(function ($sql, $bindings) {
            if (str_contains($sql, '`gestao_acessos_sessoes`')) {
                preg_match('/\(([^)]+)\)/', $sql, $match);
                preg_match_all('/`([^`]+)`/', $match[1], $columns);
                $this->assertNull(array_combine($columns[1], $bindings)['login_em']);
            }
            return true;
        })->andReturn(1);
        $connection->shouldReceive('update')->once()->andReturn(1);
        $request = $this->request();
        (new SessaoAcessoService)->registrarPerfil($request, (object) [
            'regiao_id' => 8, 'perfil_nome' => 'Pastor', 'instituicao_nome' => 'Igreja Teste',
        ]);
        $this->assertNull($request->session()->get('gestao_sessao_acesso.login_em'));
    }

    public function test_session_without_tracking_cannot_close_another_session(): void
    {
        DB::shouldReceive('table')->never();
        $this->assertNull((new SessaoAcessoService)->finalizar($this->request()));
    }

    public function test_report_uses_logged_region_and_preserves_unclosed_sessions(): void
    {
        $connection = $this->connection();
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('`p`.`regiao_id` = ?', $sql);
            $this->assertStringContainsString('`s`.`id` = `p`.`sessao_id`', $sql);
            $this->assertStringContainsString('`p`.`primeiro_uso` < ?', $sql);
            $this->assertStringContainsString('(`p`.`ultimo_uso` >= ? or `s`.`logout_em` >= ?)', $sql);
            $this->assertSame([8, '2026-12-01 00:00:00', '2026-11-01 00:00:00', '2026-11-01 00:00:00'], array_slice($bindings, 0, 4));
            $this->assertStringContainsString('`audits` as `a`', $sql);
            $this->assertStringContainsString('`a`.`event` = ?', $sql);
            $this->assertStringContainsString('`outro`.`user_id` = `a`.`user_id`', $sql);
            $this->assertStringContainsString('`registrada`.`user_id` = `a`.`user_id`', $sql);
            $this->assertStringContainsString('JSON_VALID(a.new_values)', $sql);
            $this->assertContains('login', $bindings);
            $this->assertNotContains('logout', $bindings);
            return true;
        })->andReturn([(object) ['aggregate' => 0]]);
        $this->assertSame(0, (new GestaoAcessosService)->sessoes(8, '2026-11-01', '2026-11-30')->total());
    }

    public function test_report_labels_login_and_logout_without_reintroducing_audit_or_monthly_panels(): void
    {
        $view = file_get_contents(resource_path('views/regiao/relatorios/gestao-acessos.blade.php'));
        $this->assertStringContainsString('Primeiro acesso (login)', $view);
        $this->assertStringContainsString('Último acesso (logout)', $view);
        $this->assertStringContainsString('Saída não registrada', $view);
        $this->assertStringContainsString('Login não registrado (sessão anterior)', $view);
        $this->assertStringNotContainsString('Histórico de auditoria', $view);
        $this->assertStringNotContainsString('Pastores no módulo Igreja Local', $view);
        $this->assertStringNotContainsString('SDs no módulo de Gestão Distrital', $view);
    }

    /** @dataProvider userNames */
    public function test_user_search_filters_combined_sources_using_bound_literal_name(string $nome, string $binding): void
    {
        $connection = $this->connection();
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) use ($nome, $binding) {
            $this->assertStringContainsString('union all', $sql);
            $this->assertStringContainsString("acessos.usuario LIKE ? ESCAPE '!'", $sql);
            $this->assertStringNotContainsString(trim($nome), $sql);
            $this->assertSame($binding, end($bindings));
            $this->assertStringContainsString('`p`.`regiao_id` = ?', $sql);
            $this->assertSame(8, $bindings[0]);
            return true;
        })->andReturn([(object) ['aggregate' => 0]]);
        $this->assertSame(0, (new GestaoAcessosService)->sessoes(8, '2026-11-01', '2026-11-30', $nome)->total());
    }

    public function userNames(): array
    {
        return [[' Marcos ', '%Marcos%'], ["O'Neil_%!", "%O'Neil!_!%!!%"]];
    }
}
