<?php

namespace Tests\Unit;

use App\Exceptions\DeleteMembroException;
use App\Models\MembresiaMembro;
use App\Services\ServiceMembros\DeletarMembroService;
use Carbon\Carbon;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class ExclusaoMembroIgrejaTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @dataProvider currentRollProvider */
    public function test_exclusion_updates_only_current_church_even_with_stale_roll_number(int $numeroRol): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-13 12:00:00'));
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
                'numero_rol' => 99, 'status' => 'A', 'dt_exclusao' => null, 'dt_recepcao' => '2024-08-29',
            ]]);
        $connection->shouldReceive('update')->once()
            ->withArgs(function ($sql, $bindings) {
                return str_contains($sql, 'update `membresia_rolpermanente`')
                    && str_contains($sql, 'where `id` = ?')
                    && end($bindings) === 200
                    && in_array('I', $bindings, true)
                    && count(array_filter($bindings, fn ($value) => is_string($value) && str_starts_with($value, '2026-10-13'))) > 0;
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

    /** @dataProvider exclusionDateProvider */
    public function test_today_is_compared_to_reception_in_current_church(string $dataAtual, bool $permitida): void
    {
        Carbon::setTestNow(Carbon::parse($dataAtual.' 12:00:00'));
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql) => str_contains($sql, '`membresia_membros`'))
            ->andReturn([(object) ['id' => 'membro-teste', 'igreja_id' => 20, 'rol_atual' => 10, 'status' => 'A']]);
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql, $bindings) => str_contains($sql, '`igreja_id` = ?') && $bindings === ['membro-teste', 20])
            ->andReturn([(object) [
                'id' => 200, 'membro_id' => 'membro-teste', 'igreja_id' => 20,
                'dt_recepcao' => '2026-08-29', 'status' => 'A', 'dt_exclusao' => null,
            ]]);
        DB::shouldReceive('beginTransaction')->once();
        if ($permitida) {
            $connection->shouldReceive('update')->once()
                ->withArgs(fn ($sql, $bindings) => str_contains($sql, 'update `membresia_rolpermanente`')
                    && end($bindings) === 200
                    && count(array_filter($bindings, fn ($value) => is_string($value) && str_starts_with($value, $dataAtual))) > 0)
                ->andReturn(1);
            $connection->shouldReceive('update')->once()
                ->withArgs(fn ($sql, $bindings) => str_contains($sql, 'update `membresia_membros`') && end($bindings) === 'membro-teste')
                ->andReturn(1);
            DB::shouldReceive('commit')->once();
            DB::shouldReceive('rollback')->never();
        } else {
            $connection->shouldNotReceive('update');
            DB::shouldReceive('commit')->never();
            DB::shouldReceive('rollback')->once();
        }

        try {
            $this->withModelConnection($connection, function () {
                (new DeletarMembroService)->execute([
                    'modo_exclusao_id' => 1, 'dt_exclusao' => '2001-01-01',
                ], 'membro-teste');
            });
            $this->assertTrue($permitida, 'Uma exclusao anterior a recepcao foi aceita.');
        } catch (ValidationException $e) {
            $this->assertFalse($permitida);
            $this->assertArrayHasKey('dt_exclusao', $e->errors());
            $this->assertStringContainsString('29/08/2026', $e->errors()['dt_exclusao'][0]);
        }
    }

    public function exclusionDateProvider(): array
    {
        return [
            'reported case: August 14 before August 29' => ['2026-08-14', false],
            'day before reception' => ['2026-08-28', false],
            'same day as reception' => ['2026-08-29', true],
            'day after reception' => ['2026-08-30', true],
        ];
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
