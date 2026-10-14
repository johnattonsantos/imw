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
    public function test_edits_cannot_overwrite_existing_dates_or_other_church_records(string $class, string $status): void
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
            ->withArgs(function ($sql, $bindings) {
                $this->assertStringNotContainsString('`dt_recepcao` =', $sql);
                $this->assertStringNotContainsString('`dt_exclusao` =', $sql);
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
}
