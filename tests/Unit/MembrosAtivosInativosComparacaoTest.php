<?php

namespace Tests\Unit;

use App\Services\ServiceEstatisticas\MembrosAtivosInativosService;
use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class MembrosAtivosInativosComparacaoTest extends TestCase
{
    /** @dataProvider periodProvider */
    public function test_counts_initial_and_final_dates_for_the_entire_period(int $periodo, string $inicio): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        $connection->shouldReceive('select')->once()
            ->withArgs(fn ($sql) => str_contains($sql, 'from `instituicoes_instituicoes`'))
            ->andReturn([(object) ['id' => 1, 'nome' => 'Regiao Teste']]);
        foreach ([$inicio => [], '2026-10-05' => [(object) ['instituicao_id' => 1, 'ativos' => 4, 'inativos' => 2]]] as $data => $contagens) {
            $connection->shouldReceive('select')->once()
                ->withArgs(function ($sql, $bindings) use ($data) {
                    return str_contains($sql, 'from `membresia_rolpermanente` as `rol`')
                        && str_contains($sql, '`posterior`.`regiao_id` = `rol`.`regiao_id`')
                        && $bindings === [$data, $data, 1, $data, 1, $data];
                })->andReturn($contagens);
        }

        $linhas = (new MembrosAtivosInativosService)->execute(1, $periodo, 'regiao', Carbon::parse('2026-10-05'));
        $this->assertCount(1, $linhas);
        $this->assertSame([
            'instituicao' => 'Regiao Teste', 'inicio' => $inicio, 'fim' => '2026-10-05',
            'ativos_iniciais' => 0, 'inativos_iniciais' => 0,
            'ativos_finais' => 4, 'inativos_finais' => 2,
        ], $linhas[0]);
    }

    public function periodProvider(): array
    {
        return [
            'anuenio' => [1, '2025-11-01'],
            'bienio' => [2, '2024-11-01'],
            'sexenio' => [6, '2020-11-01'],
        ];
    }
}
