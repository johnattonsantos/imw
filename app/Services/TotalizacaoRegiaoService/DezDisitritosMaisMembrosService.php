<?php

namespace App\Services\TotalizacaoRegiaoService;


use App\Traits\Identifiable;
use App\Traits\TotalizacaoRegiaoUtils;
use Carbon\Carbon;

class DezDisitritosMaisMembrosService
{
    use Identifiable;


    public function execute($dataFinal, $dataInicial)
    {

        $dataInicial ??= Carbon::now()->format('Y-m-d');
        $dataFinal ??= Carbon::now()->format('Y-m-d');
        $regiao = Identifiable::fetchtSessionRegiao();

        return [
            'lancamentos' => TotalizacaoRegiaoUtils::fetchDezDistritoMembros($dataFinal, $dataInicial, $regiao->id),
            'distritos'   => Identifiable::fetchDistritosByRegiao($regiao->id),
            'regiao'      => $regiao
        ];
    }
}
