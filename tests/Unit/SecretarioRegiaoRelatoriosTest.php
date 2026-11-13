<?php

namespace Tests\Unit;

use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class SecretarioRegiaoRelatoriosTest extends TestCase
{
    private function migration()
    {
        return require database_path('migrations/2026_11_13_120000_grant_non_financial_regional_reports_to_secretary.php');
    }

    private function rules(string $property): array
    {
        $migration = $this->migration();
        $reflection = new \ReflectionProperty($migration, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($migration);
    }

    public function test_all_non_financial_menu_permissions_are_granted(): void
    {
        $menu = file_get_contents(resource_path('views/template/menu-sidebar.blade.php'));
        $start = strpos($menu, "__('Relatórios Regionais')");
        $end = strpos($menu, "hasPerfilRegra('juridico-regiao')", $start);
        preg_match_all("/hasPerfilRegra\('([^']+)'\)/", substr($menu, $start, $end - $start), $matches);
        $covered = array_merge($this->rules('reports'), $this->rules('financialReports'));
        foreach (array_unique($matches[1]) as $rule) {
            $this->assertContains($rule, $covered, 'Unclassified report permission: '.$rule);
        }
        $this->assertSame([], array_intersect($this->rules('reports'), $this->rules('financialReports')));
    }

    public function test_non_financial_routes_use_permissions_granted_to_secretary(): void
    {
        $granted = $this->rules('reports');
        foreach (app('router')->getRoutes() as $route) {
            if (!str_starts_with($route->uri(), 'regiao/relatorio/')) {
                continue;
            }
            $middleware = $route->gatherMiddleware();
            if (in_array('seguranca:regiao-menu-relatorio-financeiro', $middleware, true)) {
                continue;
            }
            foreach ($middleware as $guard) {
                if (str_starts_with($guard, 'seguranca:')) {
                    $this->assertContains(substr($guard, 10), $granted, $route->uri());
                }
            }
        }
    }

    public function test_financial_reports_and_pdf_exports_require_financial_permission(): void
    {
        $financial = ['lancamentodasigrejas', 'saldodasigrejas', 'livrorazaogeral', 'orcamento',
            'variacaofinanceira', 'financeiro-por-categoria', 'irrf', 'financeiro/balancete', 'cota-orcamentaria'];
        $count = 0;
        foreach (app('router')->getRoutes() as $route) {
            foreach ($financial as $path) {
                if (in_array($route->uri(), ['regiao/relatorio/'.$path, 'regiao/relatorio/'.$path.'/pdf'], true)) {
                    $this->assertContains('seguranca:regiao-menu-relatorio-financeiro', $route->gatherMiddleware(), $route->uri());
                    $count++;
                }
            }
        }
        $this->assertSame(14, $count);
    }

    public function test_migration_scopes_grants_and_financial_revocations_to_regional_secretary(): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('transaction')->once()->andReturnUsing(fn ($callback) => $callback());
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        $connection->shouldReceive('select')->andReturnUsing(function ($sql, $bindings) {
            if (str_contains($sql, 'from `perfils`')) {
                $this->assertStringContainsString('`nivel` = ?', $sql);
                $this->assertStringContainsString('`deleted_at` is null', $sql);
                $this->assertSame('R', $bindings[0]);
                $this->assertContains('Secretário(a) Região', $bindings);
                $this->assertNotContains('Administrador Região', $bindings);
                return [(object) ['id' => 7]];
            }
            if (str_contains($sql, 'from `regras`')) {
                $financial = in_array('regiao-menu-relatorio-financeiro', $bindings, true);
                $this->assertSame($financial ? $this->rules('financialReports') : $this->rules('reports'), $bindings);
                return [(object) ['id' => $financial ? 99 : 12]];
            }
            $this->assertStringContainsString('exists', $sql);
            $this->assertSame([7, 12], $bindings);
            return [(object) ['exists' => 0]];
        });
        $connection->shouldReceive('insert')->once()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('insert into `perfil_regra`', $sql);
            $this->assertSame([7, 12], array_slice($bindings, 0, 2));
            return true;
        })->andReturnTrue();
        $connection->shouldReceive('delete')->once()->withArgs(function ($sql, $bindings) {
            $this->assertStringContainsString('`perfil_id` in (?)', $sql);
            $this->assertStringContainsString('`regra_id` in (?)', $sql);
            $this->assertSame([7, 99], $bindings);
            return true;
        })->andReturn(1);

        $this->migration()->up();
    }
}
