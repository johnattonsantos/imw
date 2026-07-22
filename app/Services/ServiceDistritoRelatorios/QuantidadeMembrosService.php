<?php

namespace App\Services\ServiceDistritoRelatorios;

use App\Traits\QuantidadeMembrosUtils;
use Carbon\Carbon;

class QuantidadeMembrosService
{
    use QuantidadeMembrosUtils;

    public function execute($dataInicial, $dataFinal, $periodoAnos = null)
    {
        [$dataInicial, $dataFinal, $periodoAnos] = $this->normalizarPeriodo($dataInicial, $dataFinal, $periodoAnos);

        $distritoId = session()->get('session_perfil')->instituicao_id;

        $lancamentos = QuantidadeMembrosUtils::fetch($dataInicial, $dataFinal, $distritoId);

        return [
            'lancamentos' => $lancamentos,
            'dataInicial' => $dataInicial,
            'dataFinal'   => $dataFinal,
            'periodoAnos' => $periodoAnos,
        ];
    }

    private function normalizarPeriodo($dataInicial, $dataFinal, $periodoAnos): array
    {
        $dataFinal = $dataFinal ? Carbon::parse($dataFinal) : Carbon::now();

        if ($periodoAnos !== null && $periodoAnos !== '') {
            $periodoAnos = max(1, min(6, (int) $periodoAnos));
            $dataInicial = $dataFinal->copy()->subYearsNoOverflow($periodoAnos);

            return [$dataInicial->format('Y-m-d'), $dataFinal->format('Y-m-d'), $periodoAnos];
        }

        $dataInicial = $dataInicial ? Carbon::parse($dataInicial) : $dataFinal->copy()->subYearsNoOverflow(1);
        $limiteInicial = $dataFinal->copy()->subYearsNoOverflow(6);

        if ($dataInicial->lt($limiteInicial) || $dataInicial->gt($dataFinal)) {
            $dataInicial = $limiteInicial;
        }

        return [$dataInicial->format('Y-m-d'), $dataFinal->format('Y-m-d'), null];
    }
}
