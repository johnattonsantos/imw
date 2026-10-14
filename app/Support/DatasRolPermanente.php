<?php

namespace App\Support;

class DatasRolPermanente
{
    public static function paraRegistro(?string $recepcao, ?string $exclusao, bool $inativo, bool $novo = false): array
    {
        $hoje = now()->toDateString();

        return [
            'dt_recepcao' => !$novo && $recepcao ? substr($recepcao, 0, 10) : $hoje,
            'dt_exclusao' => $inativo ? (!$novo && $exclusao ? substr($exclusao, 0, 10) : $hoje) : null,
        ];
    }
}
