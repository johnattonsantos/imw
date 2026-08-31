<?php

namespace App\Support;

use Carbon\Carbon;

class PeriodoEclesiastico
{
    public static function porQuantidadeAnos(int $periodoAnos, ?Carbon $referencia = null): array
    {
        return match ($periodoAnos) {
            1 => self::anuenioCorrente($referencia),
            2 => self::bienioCorrente($referencia),
            6 => self::sexenioCorrente($referencia),
            default => self::periodoMovel($periodoAnos, $referencia),
        };
    }

    public static function anuenioCorrente(?Carbon $referencia = null): array
    {
        $dataFinal = self::dataFinal($referencia);
        $anoInicio = $dataFinal->format('m-d') < '11-01'
            ? (int) $dataFinal->format('Y') - 1
            : (int) $dataFinal->format('Y');

        return [Carbon::create($anoInicio, 11, 1)->startOfDay(), $dataFinal];
    }

    public static function bienioCorrente(?Carbon $referencia = null): array
    {
        $dataFinal = self::dataFinal($referencia);
        $anoBase = $dataFinal->format('m-d') < '11-01'
            ? (int) $dataFinal->format('Y') - 1
            : (int) $dataFinal->format('Y');
        $anoInicio = $anoBase - ($anoBase % 2);

        return [Carbon::create($anoInicio, 11, 1)->startOfDay(), $dataFinal];
    }

    public static function sexenioCorrente(?Carbon $referencia = null): array
    {
        $dataFinal = self::dataFinal($referencia);
        $anoEclesiastico = $dataFinal->format('m-d') < '11-01'
            ? (int) $dataFinal->format('Y')
            : (int) $dataFinal->format('Y') + 1;

        $anoInicio = $anoEclesiastico - ((($anoEclesiastico % 6) - 4 + 6) % 6);
        $dataInicial = Carbon::create($anoInicio, 11, 1)->startOfDay();

        if ($dataInicial->gt($dataFinal)) {
            $dataInicial->subYears(6);
        }

        return [$dataInicial, $dataFinal];
    }

    private static function dataFinal(?Carbon $referencia): Carbon
    {
        return ($referencia ? $referencia->copy() : Carbon::now())->endOfDay();
    }

    private static function periodoMovel(int $periodoAnos, ?Carbon $referencia): array
    {
        $dataFinal = self::dataFinal($referencia);
        $dataInicial = $dataFinal->copy()->subYearsNoOverflow(max(1, min(6, $periodoAnos)))->startOfDay();

        return [$dataInicial, $dataFinal];
    }
}
