<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\InstituicoesInstituicao;
use App\Traits\Identifiable;
use App\Traits\QuantidadeMembrosUtils;
use Carbon\Carbon;

class QuantidadeMembrosService
{
    use QuantidadeMembrosUtils;
    use Identifiable;

    public function execute($dataInicial, $dataFinal, $distritoId, $periodoAnos = null)
    {
        [$dataInicial, $dataFinal, $periodoAnos] = $this->normalizarPeriodo($dataInicial, $dataFinal, $periodoAnos);

        $regiao = Identifiable::fetchtSessionRegiao();

        return [
            'lancamentos' => QuantidadeMembrosUtils::fetch($dataInicial, $dataFinal, $distritoId, $regiao->id),
            'distritos'   => Identifiable::fetchDistritosByRegiao($regiao->id),
            'instituicao' => InstituicoesInstituicao::find($distritoId),
            'regiao'      => $regiao,
            'dataInicial' => $dataInicial,
            'dataFinal'   => $dataFinal,
            'periodoAnos' => $periodoAnos,
            'distritoId'  => $distritoId,
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
