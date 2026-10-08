<?php

namespace Tests\Unit;

use App\Services\ServiceClerigosRegiao\PesquisarClerigosPorCpfService;
use PHPUnit\Framework\TestCase;

class PesquisarClerigosPorCpfTest extends TestCase
{
    public function test_separates_logged_region_and_detects_cross_region_registrations(): void
    {
        $resultado = (new PesquisarClerigosPorCpfService)->separarPorRegiao(collect([
            (object) ['nome' => 'Local', 'regiao_id' => '10', 'deleted_at' => null],
            (object) ['nome' => 'Outra', 'regiao_id' => 20, 'deleted_at' => '2025-01-01'],
        ]), 10);

        $this->assertSame('Local', $resultado['daRegiao']->first()->nome);
        $this->assertSame('Outra', $resultado['outrasRegioes']->first()->nome);
        $this->assertTrue($resultado['multiplasRegioes']);
        $this->assertFalse($resultado['duplicadoNaRegiao']);
    }

    public function test_same_region_duplicates_are_not_cross_region_duplicates(): void
    {
        $resultado = (new PesquisarClerigosPorCpfService)->separarPorRegiao(collect([
            (object) ['regiao_id' => 10], (object) ['regiao_id' => 10],
        ]), 10);

        $this->assertTrue($resultado['duplicadoNaRegiao']);
        $this->assertFalse($resultado['multiplasRegioes']);
        $this->assertCount(2, $resultado['daRegiao']);
    }

    public function test_can_find_clergy_only_in_another_region(): void
    {
        $resultado = (new PesquisarClerigosPorCpfService)->separarPorRegiao(collect([
            (object) ['regiao_id' => 20],
        ]), 10);

        $this->assertCount(0, $resultado['daRegiao']);
        $this->assertCount(1, $resultado['outrasRegioes']);
        $this->assertFalse($resultado['multiplasRegioes']);
    }

    public function test_no_matches_does_not_show_duplicate_alerts(): void
    {
        $resultado = (new PesquisarClerigosPorCpfService)->separarPorRegiao(collect(), 10);

        $this->assertFalse($resultado['multiplasRegioes']);
        $this->assertFalse($resultado['duplicadoNaRegiao']);
    }
}
