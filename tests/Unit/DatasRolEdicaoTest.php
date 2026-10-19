<?php

namespace Tests\Unit;

use App\Services\ServiceMembrosGeral\UpdateMembroService;
use App\Services\ServiceMembrosGeral\UpdateMembroRecadastramentoService;
use Carbon\Carbon;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;
use Mockery;
use Tests\TestCase;

class DatasRolEdicaoTest extends TestCase
{
    /** @dataProvider serviceProvider */
    public function test_historical_dates_are_editable_only_in_recadastramento(string $class, string $status): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-14 12:00:00'));
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql) => str_contains($sql, '`membresia_membros`'))
            ->andReturn([(object) ['id' => 'teste', 'igreja_id' => 20, 'distrito_id' => 2, 'regiao_id' => 1, 'status' => $status]]);
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql, $bindings) => str_contains($sql, '`igreja_id` = ?') && $bindings === ['teste', 20])
            ->andReturn([(object) [
                'id' => 200, 'membro_id' => 'teste', 'igreja_id' => 20,
                'status' => $status, 'dt_recepcao' => '2001-09-01',
                'dt_exclusao' => $status === 'I' ? '2024-08-29' : null,
                'numero_rol' => 10, 'modo_recepcao_id' => 1, 'modo_exclusao_id' => $status === 'I' ? 2 : null,
            ]]);
        $connection->shouldReceive('update')->once()
            ->withArgs(function ($sql, $bindings) use ($class, $status) {
                if ($class === UpdateMembroRecadastramentoService::class) {
                    $this->assertStringContainsString('`dt_recepcao` =', $sql);
                    $this->assertTrue(count(array_filter($bindings, fn ($valor) => is_string($valor) && str_starts_with($valor, '1999-01-01'))) > 0);
                    if ($status === 'I') {
                        $this->assertStringContainsString('`dt_exclusao` =', $sql);
                        $this->assertTrue(count(array_filter($bindings, fn ($valor) => is_string($valor) && str_starts_with($valor, '1999-02-01'))) > 0);
                    }
                } else {
                    $this->assertStringNotContainsString('`dt_recepcao` =', $sql);
                    $this->assertStringNotContainsString('`dt_exclusao` =', $sql);
                }
                $this->assertSame(200, end($bindings));
                return true;
            })->andReturn(1);

        $original = Model::getConnectionResolver();
        $resolver = new ConnectionResolver(['test' => $connection]);
        $resolver->setDefaultConnection('test');
        Model::setConnectionResolver($resolver);
        try {
            $method = new \ReflectionMethod($class, 'updateDadosRolPermanente');
            $method->setAccessible(true);
            Model::withoutEvents(fn () => $method->invoke(new $class, [
                'dt_recepcao' => '1999-01-01', 'dt_exclusao' => '1999-02-01',
                'modo_recepcao_id' => 4, 'rol_atual' => 99, 'status' => $status,
            ], 'teste'));
        } finally {
            Model::setConnectionResolver($original);
            Carbon::setTestNow();
        }
    }

    public function serviceProvider(): array
    {
        return [
            'regular edit, active' => [UpdateMembroService::class, 'A'],
            'regular edit, inactive' => [UpdateMembroService::class, 'I'],
            'validation edit, active' => [UpdateMembroRecadastramentoService::class, 'A'],
            'validation edit, inactive' => [UpdateMembroRecadastramentoService::class, 'I'],
        ];
    }

    public function test_recadastramento_request_preserves_submitted_historical_dates(): void
    {
        $request = new \App\Http\Requests\UpdateMembroRequest;
        $route = new \Illuminate\Routing\Route('POST', 'secretaria/recadastramento-membro/atualizar/{id}', []);
        $route->name('recadastramento-membro.update');
        $request->setRouteResolver(fn () => $route);
        $request->merge(['dt_recepcao' => '2001-09-01', 'dt_exclusao' => '2024-08-29', 'rol_atual' => '0']);
        $method = new \ReflectionMethod($request, 'prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);
        $this->assertSame('2001-09-01', $request->input('dt_recepcao'));
        $this->assertSame('2024-08-29', $request->input('dt_exclusao'));
        $this->assertNull($request->input('rol_atual'));
    }
}
