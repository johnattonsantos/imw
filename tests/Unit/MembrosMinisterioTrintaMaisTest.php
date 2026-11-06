<?php

namespace Tests\Unit;

use App\Traits\MembrosMinisterioUtils;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class MembrosMinisterioTrintaMaisTest extends TestCase
{
    public function test_thirty_to_fifty_nine_readings_exclude_seniors_without_duplicating_totals(): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        $row = (object) [
            'Kids_X' => 0, 'Conexao_X' => 0, 'Fire_X' => 0, 'Move_X' => 2,
            'Homens_X' => 1, 'Mulheres_X' => 1, 'Sessenta_X' => 2,
            'Kids_Y' => 0, 'Conexao_Y' => 0, 'Fire_Y' => 0, 'Move_Y' => 2,
            'Homens_Y' => 1, 'Mulheres_Y' => 1, 'Sessenta_Y' => 2,
        ];
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) {
            $this->assertStringNotContainsString('Homens30_', $sql);
            $this->assertStringNotContainsString('Mulheres30_', $sql);
            $this->assertSame(14, substr_count($sql, 'COUNT(CASE WHEN'));
            foreach (['Homens' => 'M', 'Mulheres' => 'F'] as $nome => $sexo) {
                foreach (['X' => '2025-11-01', 'Y' => '2026-10-22'] as $sufixo => $data) {
                    preg_match('/COUNT\(CASE WHEN [^`]+ AS `'.$nome.'_'.$sufixo.'`/', $sql, $matches, PREG_OFFSET_CAPTURE);
                    $this->assertNotEmpty($matches);
                    [$expression, $offset] = $matches[0];
                    $this->assertStringContainsString('TIMESTAMPDIFF(YEAR, mm.data_nascimento, ?) BETWEEN ? AND ?', $expression);
                    $bindingOffset = substr_count(substr($sql, 0, $offset), '?');
                    $this->assertSame([$data, 30, 59, $data, $data, $sexo], array_slice($bindings, $bindingOffset, 6));
                }
            }
            return true;
        })->andReturn([$row]);

        $service = new class {
            use MembrosMinisterioUtils;
        };
        $resultado = $service::fetch('2025-11-01', '2026-10-22', 'M', 2)->first();
        $this->assertSame(6, $resultado->Total_X);
        $this->assertSame(6, $resultado->Total_Y);
        $this->assertSame(1, $resultado->Homens_X);
        $this->assertSame(1, $resultado->Mulheres_Y);
    }
}
