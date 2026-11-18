<?php

namespace Tests\Unit;

use App\Http\Controllers\GestaoAcessosController;
use App\Http\Middleware\RegistrarGestaoAcessos;
use App\Models\User;
use App\Services\ServiceRegiaoRelatorios\GestaoAcessosService;
use App\Services\ServiceRegiaoRelatorios\RegistrarAcessoService;
use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class GestaoAcessosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-18 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @dataProvider monthStates */
    public function test_monthly_status_does_not_invent_missing_history(int $ano, int $mes, int $dias, ?string $inicio, bool $usuario, string $expected): void
    {
        $this->assertSame($expected, GestaoAcessosService::situacaoMes($ano, $mes, $dias, $inicio, $usuario));
    }

    public function monthStates(): array
    {
        return [
            [2026, 10, 0, '2026-11-01 00:00:00', true, 'Sem histórico'],
            [2026, 11, 0, '2026-11-18 10:00:00', true, 'Sem registro (cobertura parcial)'],
            [2026, 11, 2, '2026-11-18 10:00:00', true, 'Acessou (2 dia(s))'],
            [2026, 11, 0, '2026-10-01 00:00:00', true, 'Sem acesso até agora'],
            [2026, 10, 0, '2026-09-01 00:00:00', true, 'Não acessou'],
            [2026, 12, 0, '2026-09-01 00:00:00', true, 'Período futuro'],
            [2026, 11, 0, null, true, 'Sem histórico'],
            [2026, 11, 0, '2026-11-01 00:00:00', false, 'Sem usuário/perfil cadastrado'],
        ];
    }

    /** @dataProvider groups */
    public function test_queries_are_scoped_to_region_in_both_roster_and_history(?string $grupo): void
    {
        $connection = $this->connection();
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) use ($grupo) {
            $this->assertStringContainsString('`regiao_id` = ?', $sql);
            $this->assertStringContainsString('`pai`.`instituicao_pai_id` = ?', $sql);
            $this->assertStringContainsString('`i`.`instituicao_pai_id` = ?', $sql);
            $this->assertStringContainsString('`i`.`id` = ?', $sql);
            $this->assertStringContainsString('`a`.`user_id` = `conta`.`user_id`', $sql);
            $this->assertStringContainsString('`a`.`perfil_id` = `conta`.`perfil_id`', $sql);
            $this->assertStringContainsString('`a`.`instituicao_id` = `i`.`id`', $sql);
            $this->assertContains(8, $bindings);
            $this->assertContains('2026-12-31', $bindings);
            if ($grupo === 'pastores') {
                $this->assertContains('Pastor', $bindings);
            } elseif ($grupo === 'sds') {
                $this->assertContains('Administrador Distrito', $bindings);
            }
            return true;
        })->andReturn([]);
        $this->assertCount(0, (new GestaoAcessosService)->quadro(8, 2026, '2026-11-01', '2026-11-30', $grupo));
    }

    public function groups(): array
    {
        return [[null], ['pastores'], ['sds']];
    }

    public function test_recording_is_daily_and_throttled_without_overwriting_first_access(): void
    {
        session(['session_perfil' => (object) ['perfil_id' => 7, 'instituicao_id' => 10]]);
        $request = Request::create('/dashboard');
        $request->setLaravelSession(app('session.store'));
        $request->setUserResolver(fn () => (new User)->forceFill(['id' => 5]));
        $connection = $this->connection();
        $sessoes = Mockery::mock(\App\Services\ServiceRegiaoRelatorios\SessaoAcessoService::class);
        $sessoes->shouldReceive('registrarPerfil')->twice();
        $this->app->instance(\App\Services\ServiceRegiaoRelatorios\SessaoAcessoService::class, $sessoes);
        $connection->shouldReceive('select')->twice()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('`pu`.`user_id` = ?', $sql);
            $this->assertStringContainsString('`pu`.`perfil_id` = ?', $sql);
            $this->assertSame([5, 7, 10, 1, 2, 3], $bindings);
            return true;
        })->andReturn([(object) ['regiao_id' => 8]]);
        $connection->shouldReceive('affectingStatement')->twice()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('insert ignore into `gestao_acessos`', $sql);
            $this->assertStringContainsString('`primeiro_acesso`', $sql);
            return true;
        })->andReturn(1);
        $connection->shouldReceive('update')->twice()->withArgs(function ($sql) {
            $this->assertStringContainsString('set `ultimo_acesso` = ?', $sql);
            $this->assertStringNotContainsString('set `primeiro_acesso`', $sql);
            return true;
        })->andReturn(1);
        $service = new RegistrarAcessoService;
        $service->registrar($request);
        $service->registrar($request);
        Carbon::setTestNow('2026-11-19 00:01:00');
        $service->registrar($request);
    }

    /** @dataProvider skippedRequests */
    public function test_non_navigation_requests_do_not_record(string $method, string $path, int $status, bool $ajax, bool $auth): void
    {
        $request = Request::create($path, $method);
        if ($ajax) {
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        }
        $route = new Route($method, ltrim($path, '/'), fn () => null);
        if ($auth) {
            $route->middleware('auth');
        }
        $request->setRouteResolver(fn () => $route);
        $service = Mockery::mock(RegistrarAcessoService::class);
        $service->shouldNotReceive('registrar');
        $this->app->instance(RegistrarAcessoService::class, $service);
        $response = (new RegistrarGestaoAcessos)->handle($request, fn () => new Response('', $status));
        $this->assertSame($status, $response->getStatusCode());
    }

    public function skippedRequests(): array
    {
        return [['POST', '/dashboard', 200, false, true], ['GET', '/dashboard', 403, false, true],
            ['GET', '/dashboard', 302, false, true], ['GET', '/dashboard', 200, true, true],
            ['GET', '/selecionarPerfil', 200, false, true], ['GET', '/validar-membro', 200, false, false]];
    }

    public function test_successful_authenticated_navigation_records_presence(): void
    {
        $request = Request::create('/dashboard');
        $route = (new Route('GET', 'dashboard', fn () => null))->middleware('auth');
        $request->setRouteResolver(fn () => $route);
        $service = Mockery::mock(RegistrarAcessoService::class);
        $service->shouldReceive('registrar')->once()->with($request);
        $this->app->instance(RegistrarAcessoService::class, $service);
        $this->assertSame(200, (new RegistrarGestaoAcessos)->handle($request, fn () => new Response)->getStatusCode());
    }

    public function test_unauthorized_profiles_cannot_read_report(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('podeVerGestaoAcessos')->once()->andReturnFalse();
        $request = Request::create('/regiao/relatorio/gestao-acessos', 'GET', ['regiao_id' => 99]);
        $request->setUserResolver(fn () => $user);
        $this->expectException(HttpException::class);
        (new GestaoAcessosController)->index($request, Mockery::mock(GestaoAcessosService::class));
    }

    public function test_regional_admin_profile_cannot_be_used_from_local_institution(): void
    {
        session(['session_perfil' => (object) ['perfil_id' => 3, 'instituicao_id' => 100,
            'instituicoes' => (object) ['regiao' => (object) ['id' => 8]]]]);
        $this->assertFalse((new User)->podeVerGestaoAcessos());
    }

    public function test_invalid_day_is_rejected_before_querying_history(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('podeVerGestaoAcessos')->andReturnTrue();
        $request = Request::create('/', 'GET', ['ano' => 2026, 'mes' => 2, 'dia' => 30]);
        $request->setUserResolver(fn () => $user);
        DB::shouldReceive('table')->never();
        $this->expectException(ValidationException::class);
        (new GestaoAcessosController)->index($request, Mockery::mock(GestaoAcessosService::class));
    }

    public function test_selected_region_is_used_instead_of_url_region(): void
    {
        $regiao = (new \App\Models\InstituicoesInstituicao)->forceFill(['id' => 8, 'nome' => 'Regiao Teste']);
        session(['session_perfil' => (object) ['instituicoes' => (object) ['regiao' => $regiao]]]);
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('podeVerGestaoAcessos')->andReturnTrue();
        $request = Request::create('/', 'GET', ['ano' => 2026, 'mes' => 11, 'dia' => 18, 'regiao_id' => 999, 'usuario' => ' Marcos ']);
        $request->setUserResolver(fn () => $user);
        $service = Mockery::mock(GestaoAcessosService::class);
        $service->shouldReceive('sessoes')->once()->with(8, '2026-11-18', '2026-11-18', 'Marcos')
            ->andReturn(new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50));
        $builder = Mockery::mock(\Illuminate\Database\Query\Builder::class);
        DB::shouldReceive('table')->once()->with('gestao_acessos_configuracao')->andReturn($builder);
        $builder->shouldReceive('where')->with('id', 1)->andReturnSelf();
        $builder->shouldReceive('value')->with('inicio_sessoes')->andReturn('2026-11-18 10:00:00');
        DB::shouldReceive('connection')->never();
        \Illuminate\Support\Facades\Response::shouldReceive('view')->once()->withArgs(function ($view, $data) {
            $this->assertSame('regiao.relatorios.gestao-acessos', $view);
            $this->assertSame(8, $data['regiao']->id);
            $this->assertSame('Marcos', $data['usuario']);
            $this->assertSame(['perfis'], array_keys($data['quadros']));
            $this->assertArrayNotHasKey('historico', $data);
            return true;
        })->andReturn(new \Illuminate\Http\Response);
        $response = (new GestaoAcessosController)->index($request, $service);
        $this->assertSame('no-store, private', $response->headers->get('Cache-Control'));
    }

    public function test_admin_authorization_requires_actual_regional_profile_assignment(): void
    {
        session(['session_perfil' => (object) ['perfil_id' => 3, 'instituicao_id' => 8,
            'instituicoes' => (object) ['regiao' => (object) ['id' => 8]]]]);
        $user = Mockery::mock(User::class)->makePartial()->forceFill(['id' => 5]);
        $user->shouldReceive('hasPerfilRegra')->once()->with('regiao-gestao-acessos')->andReturnTrue();
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('`perfil_user`.`instituicao_id` = ?', $sql);
            $this->assertStringContainsString('`perfil_user`.`perfil_id` = ?', $sql);
            $this->assertStringContainsString('`perfils`.`nivel` = ?', $sql);
            $this->assertContains('Administrador Região', $bindings);
            $this->assertNotContains('Secretário(a) Região', $bindings);
            $this->assertNotContains('Administrador SRA', $bindings);
            return true;
        })->andReturn([(object) ['exists' => 1]]);
        $original = Model::getConnectionResolver();
        $resolver = new ConnectionResolver(['test' => $connection]);
        $resolver->setDefaultConnection('test');
        Model::setConnectionResolver($resolver);
        try {
            $this->assertTrue($user->podeVerGestaoAcessos());
        } finally {
            Model::setConnectionResolver($original);
        }
    }

    private function connection()
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        return $connection;
    }
}
