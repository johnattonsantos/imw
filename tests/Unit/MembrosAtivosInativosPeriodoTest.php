<?php

namespace Tests\Unit;

use App\Services\ServiceEstatisticas\MembrosAtivosInativosService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class MembrosAtivosInativosPeriodoTest extends TestCase
{
    public function test_current_annual_period_stops_at_reference_date(): void
    {
        $datas = (new MembrosAtivosInativosService)->datas(1, Carbon::parse('2026-10-05'));
        $this->assertSame([['inicio' => '2025-11-01', 'fim' => '2026-10-05']], $datas);
    }

    public function test_biennium_includes_completed_year_and_current_year(): void
    {
        $datas = (new MembrosAtivosInativosService)->datas(2, Carbon::parse('2026-10-05'));
        $this->assertSame([
            ['inicio' => '2024-11-01', 'fim' => '2025-10-31'],
            ['inicio' => '2025-11-01', 'fim' => '2026-10-05'],
        ], $datas);
    }

    public function test_six_year_period_has_six_snapshots_without_mutating_reference(): void
    {
        $referencia = Carbon::parse('2026-10-31');
        $datas = (new MembrosAtivosInativosService)->datas(6, $referencia);
        $this->assertCount(6, $datas);
        $this->assertSame('2020-11-01', $datas[0]['inicio']);
        $this->assertSame('2026-10-31', $datas[5]['fim']);
        $this->assertSame('2026-10-31', $referencia->toDateString());
    }

    public function test_new_ecclesiastical_year_starts_on_november_first(): void
    {
        $datas = (new MembrosAtivosInativosService)->datas(1, Carbon::parse('2026-11-01'));
        $this->assertSame([['inicio' => '2026-11-01', 'fim' => '2026-11-01']], $datas);
    }

    public function test_intermediate_periods_include_requested_number_of_ecclesiastical_years(): void
    {
        foreach ([3, 4, 5] as $periodo) {
            $datas = (new MembrosAtivosInativosService)->datas($periodo, Carbon::parse('2026-10-05'));
            $this->assertCount($periodo, $datas);
            $this->assertSame((2026 - $periodo).'-11-01', $datas[0]['inicio']);
            $this->assertSame('2026-10-05', $datas[$periodo - 1]['fim']);
        }
    }
}
