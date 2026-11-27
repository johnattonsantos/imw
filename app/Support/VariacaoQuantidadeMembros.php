<?php

namespace App\Support;

class VariacaoQuantidadeMembros
{
    public static function calcular(int $inicial, int $final): ?float
    {
        if ($inicial === 0) {
            return $final === 0 ? 0.0 : null;
        }

        return round((($final - $inicial) / $inicial) * 100, 2);
    }

    public static function formatar(int $inicial, int $final): string
    {
        $percentual = self::calcular($inicial, $final);

        return $percentual === null ? '-' : rtrim(rtrim(number_format($percentual, 2, ',', '.'), '0'), ',').'%';
    }

    public static function formatarAumento(int $inicial, int $final): string
    {
        $aumento = $final - $inicial;

        return ($aumento > 0 ? '+' : '').number_format($aumento, 0, ',', '.');
    }
}
