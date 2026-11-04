<?php

namespace Tests\Unit;

use App\Traits\TotalizacaoRegiaoUtils;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class RankingCrescimentoMembrosTest extends TestCase
{
    /** @dataProvider rankingProvider */
    public function test_ranking_uses_selected_metric_before_top_ten_limit(string $metodo, string $coluna, string $criterio): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table, $as = null) => $connection->table($table, $as));
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) use ($criterio, $coluna) {
            $this->assertStringContainsString('`base`.`'.$coluna.'` = `ii`.`id`', $sql);
            $this->assertStringContainsString('`base`.`dt_recepcao` < ?', $sql);
            $this->assertStringContainsString('`base`.`dt_exclusao` >= ?', $sql);
            $this->assertStringContainsString('`encerramento`.`'.$coluna.'` = `ii`.`id`', $sql);
            $this->assertStringContainsString('`encerramento`.`dt_recepcao` <= ?', $sql);
            $this->assertStringContainsString('`encerramento`.`dt_exclusao` > ?', $sql);
            $this->assertStringContainsString('CAST(base_final AS SIGNED) - CAST(base_inicial AS SIGNED) AS total', $sql);
            $this->assertStringContainsString('limit 10', $sql);
            $this->assertStringContainsString('2026-01-01', implode('|', $bindings));
            if ($criterio === 'percentual') {
                $this->assertStringContainsString('order by `percentual` desc, `total` desc, `nome` asc limit 10', $sql);
            } else {
                $this->assertStringContainsString('order by `total` desc, `nome` asc limit 10', $sql);
            }
            return true;
        })->andReturn([
            (object) ['nome' => 'Grande', 'total' => 100, 'base_inicial' => 1000],
            (object) ['nome' => 'Pequena', 'total' => 5, 'base_inicial' => 10],
            (object) ['nome' => 'Nova', 'total' => 2, 'base_inicial' => 0],
            (object) ['nome' => 'Vazia', 'total' => 0, 'base_inicial' => 0],
            (object) ['nome' => 'Dobrou', 'total' => 1, 'base_inicial' => 1],
            (object) ['nome' => 'Reducao', 'total' => -5, 'base_inicial' => 10],
        ]);
        $service = new class {
            use TotalizacaoRegiaoUtils;
        };
        $resultado = $service::$metodo('2026-11-04', '2026-01-01', 1, $criterio)->keyBy('nome');
        $this->assertEquals(10, $resultado['Grande']->percentual);
        $this->assertEquals(50, $resultado['Pequena']->percentual);
        $this->assertNull($resultado['Nova']->percentual);
        $this->assertSame(0.0, $resultado['Vazia']->percentual);
        $this->assertEquals(100, $resultado['Dobrou']->percentual);
        $this->assertEquals(-50, $resultado['Reducao']->percentual);
    }

    public function rankingProvider(): array
    {
        return [
            ['fetchDezDistritoCresceramMembros', 'distrito_id', 'absoluto'],
            ['fetchDezDistritoCresceramMembros', 'distrito_id', 'percentual'],
            ['fetchDezIgrejaCresceramMembros', 'igreja_id', 'absoluto'],
            ['fetchDezIgrejaCresceramMembros', 'igreja_id', 'percentual'],
        ];
    }
}
