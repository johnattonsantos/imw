<?php

namespace Tests\Unit;

use App\Services\ServiceEstatisticas\MembrosAtivosInativosService;
use PHPUnit\Framework\TestCase;

class MembrosAtivosInativosCrescimentoTest extends TestCase
{
    /** @dataProvider crescimentoProvider */
    public function test_growth_compares_final_count_against_initial_count(int $inicial, int $final, ?float $esperado): void
    {
        $this->assertSame($esperado, (new MembrosAtivosInativosService)->percentualCrescimento($inicial, $final));
    }

    public function crescimentoProvider(): array
    {
        return [
            'growth' => [41, 44, 7.32],
            'decline' => [100, 75, -25.0],
            'unchanged' => [40, 40, 0.0],
            'all members left' => [10, 0, -100.0],
            'empty throughout' => [0, 0, 0.0],
            'no initial base' => [0, 10, null],
        ];
    }
}
