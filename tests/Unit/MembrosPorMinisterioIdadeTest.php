<?php

namespace Tests\Unit;

use App\Services\ServiceRelatorio\IdentificaDadosRelatorioMembrosPorMinisterioService;
use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class MembrosPorMinisterioIdadeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-22 12:00:00'));
        session(['session_perfil' => (object) ['instituicoes' => (object) ['igrejaLocal' => (object) ['id' => 20]]]]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_summary_includes_thirtieth_birthday_and_seniors_but_excludes_younger_and_unknown_ages(): void
    {
        $connection = $this->connection();
        $connection->shouldReceive('select')->once()->andReturn(array_map(fn ($dados) => (object) $dados, [
            ['sexo' => 'M', 'data_nascimento' => '1996-10-22'],
            ['sexo' => 'M', 'data_nascimento' => '1996-10-23'],
            ['sexo' => 'M', 'data_nascimento' => '1960-01-01'],
            ['sexo' => 'F', 'data_nascimento' => '1996-10-22'],
            ['sexo' => 'F', 'data_nascimento' => '1996-10-23'],
            ['sexo' => 'F', 'data_nascimento' => '1960-01-01'],
            ['sexo' => 'F', 'data_nascimento' => null],
        ]));

        $data = (new IdentificaDadosRelatorioMembrosPorMinisterioService)->execute();
        $this->assertSame(2, $data['quantidadeHomens30Mais']);
        $this->assertSame(2, $data['quantidadeMulheres30Mais']);
        $this->assertSame(7, $data['quantidadeIntegrantes']);
    }

    /** @dataProvider ministryProvider */
    public function test_gender_filters_apply_inclusive_thirty_year_cutoff(string $ministerio, string $sexo): void
    {
        $connection = $this->connection();
        $connection->shouldReceive('select')->once()->withArgs(function ($sql, $bindings) use ($sexo) {
            $this->assertStringContainsString('`mm`.`sexo` = ?', $sql);
            $this->assertStringContainsString('date(`mm`.`data_nascimento`) <= ?', $sql);
            $this->assertSame([20, 'A', 'M', $sexo, '1996-10-22'], $bindings);
            return true;
        })->andReturn([]);

        $data = (new IdentificaDadosRelatorioMembrosPorMinisterioService)->execute(['ministerio' => $ministerio]);
        $this->assertStringContainsString('30 anos ou mais', $data['ministerioNome']);
    }

    public function ministryProvider(): array
    {
        return [['homens', 'M'], ['mulheres', 'F']];
    }

    private function connection()
    {
        $connection = Mockery::mock(MySqlConnection::class)->makePartial();
        $connection->__construct(null, 'test');
        DB::shouldReceive('table')->andReturnUsing(fn ($table) => $connection->table($table));
        DB::shouldReceive('raw')->andReturnUsing(fn ($sql) => new Expression($sql));
        return $connection;
    }
}
