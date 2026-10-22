<?php

namespace Tests\Unit;

use App\Traits\MembrosMinisterioUtils;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class MembrosMinisterioTrintaMaisTest extends TestCase
{
    public function test_thirty_plus_readings_include_seniors_without_duplicating_totals(): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        $row = (object) [
            'Kids_X' => 0, 'Conexao_X' => 0, 'Fire_X' => 0, 'Move_X' => 2,
            'Homens_X' => 1, 'Mulheres_X' => 1, 'Sessenta_X' => 2,
            'Kids_Y' => 0, 'Conexao_Y' => 0, 'Fire_Y' => 0, 'Move_Y' => 2,
            'Homens_Y' => 1, 'Mulheres_Y' => 1, 'Sessenta_Y' => 2,
            'Homens30_X' => 2, 'Mulheres30_X' => 2, 'Homens30_Y' => 2, 'Mulheres30_Y' => 2,
        ];
        $connection->shouldReceive('select')->once()->withArgs(function ($sql) {
            foreach (['Homens' => 'M', 'Mulheres' => 'F'] as $nome => $sexo) {
                foreach (['X' => '2025-11-01', 'Y' => '2026-10-22'] as $sufixo => $data) {
                    $this->assertMatchesRegularExpression(
                        '/TIMESTAMPDIFF\(YEAR, mm\.data_nascimento, CURDATE\(\)\) >= 30\s+'
                        ."AND mm.sexo = '$sexo'\s+AND mr.dt_recepcao <= '$data'.*?AS {$nome}30_{$sufixo}/s",
                        $sql
                    );
                }
            }
            return true;
        })->andReturn([$row]);

        $service = new class {
            use MembrosMinisterioUtils;
        };
        $resultado = $service::fetch('2025-11-01', '2026-10-22', 'M', 2)->first();
        $this->assertSame(2, $resultado->Homens30_X);
        $this->assertSame(2, $resultado->Mulheres30_Y);
        $this->assertSame(6, $resultado->Total_X);
        $this->assertSame(6, $resultado->Total_Y);
        $this->assertSame(1, $resultado->Homens_X);
        $this->assertSame(1, $resultado->Mulheres_Y);
    }
}
