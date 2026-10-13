<?php

namespace Tests\Unit;

use App\Exceptions\DeleteMembroException;
use App\Models\MembresiaMembro;
use App\Services\ServiceMembros\DeletarMembroService;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class ExclusaoMembroIgrejaTest extends TestCase
{
    /** @dataProvider currentRollProvider */
    public function test_exclusion_updates_only_current_church_even_with_stale_roll_number(int $numeroRol): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql, $bindings) => str_contains($sql, '`membresia_membros`') && $bindings === ['membro-teste'])
            ->andReturn([(object) [
                'id' => 'membro-teste', 'igreja_id' => 20, 'rol_atual' => $numeroRol, 'status' => 'A',
            ]]);
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql, $bindings) => str_contains($sql, '`membresia_rolpermanente`')
                && str_contains($sql, '`igreja_id` = ?')
                && str_contains($sql, 'order by `dt_recepcao` desc, `id` desc')
                && $bindings === ['membro-teste', 20])
            ->andReturn([(object) [
                'id' => 200, 'membro_id' => 'membro-teste', 'igreja_id' => 20,
                'numero_rol' => 99, 'status' => 'A', 'dt_exclusao' => null,
            ]]);
        $connection->shouldReceive('update')->once()
            ->withArgs(function ($sql, $bindings) {
                return str_contains($sql, 'update `membresia_rolpermanente`')
                    && str_contains($sql, 'where `id` = ?')
                    && end($bindings) === 200
                    && in_array('I', $bindings, true)
                    && count(array_filter($bindings, fn ($value) => is_string($value) && str_starts_with($value, '2026-08-14'))) > 0;
            })->andReturn(1);
        $connection->shouldReceive('update')->once()
            ->withArgs(function ($sql, $bindings) {
                return str_contains($sql, 'update `membresia_membros`')
                    && end($bindings) === 'membro-teste'
                    && in_array('I', $bindings, true);
            })->andReturn(1);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollback')->never();
        $this->withModelConnection($connection, function () {
            (new DeletarMembroService)->execute([
                'modo_exclusao_id' => 1, 'dt_exclusao' => '2026-08-14',
            ], 'membro-teste');
        });
        $this->addToAssertionCount(1);
    }

    public function currentRollProvider(): array
    {
        return ['old roll from church A' => [10], 'correct roll from church B' => [99]];
    }

    public function test_missing_roll_in_current_church_rolls_back_instead_of_updating_church_a(): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql) => str_contains($sql, '`membresia_membros`'))
            ->andReturn([(object) ['id' => 'membro-teste', 'igreja_id' => 20, 'rol_atual' => 10]]);
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql, $bindings) => str_contains($sql, '`igreja_id` = ?') && $bindings === ['membro-teste', 20])
            ->andReturn([]);
        $connection->shouldNotReceive('update');
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollback')->once();

        $this->expectException(DeleteMembroException::class);
        $this->withModelConnection($connection, function () {
            (new DeletarMembroService)->execute([
                'modo_exclusao_id' => 1, 'dt_exclusao' => '2026-08-14',
            ], 'membro-teste');
        });
    }

    private function withModelConnection($connection, callable $callback): void
    {
        $original = Model::getConnectionResolver();
        $resolver = new ConnectionResolver(['test' => $connection]);
        $resolver->setDefaultConnection('test');
        Model::setConnectionResolver($resolver);
        try {
            Model::withoutEvents($callback);
        } finally {
            Model::setConnectionResolver($original);
        }
    }
}
