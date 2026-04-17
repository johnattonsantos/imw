<?php

namespace App\Services\TotalizacaoRegiaoService;


use App\Traits\Identifiable;
use App\Traits\TotalizacaoRegiaoUtils;
use Carbon\Carbon;

class DezDisitritosMaisReceberamMembrosService
{
    use Identifiable;


    public function execute($dataFinal, $dataInicial)
    {

        $dataInicial ??= Carbon::now()->format('Y-m-d');
        $dataFinal ??= Carbon::now()->format('Y-m-d');
        $regiao = Identifiable::fetchtSessionRegiao();

        return [
            'lancamentos' => TotalizacaoRegiaoUtils::fetchDezDistritoCresceramMembros($dataFinal, $dataInicial, $regiao->id),
            'distritos'   => Identifiable::fetchDistritosByRegiao($regiao->id),
            'regiao'      => $regiao
        ];
    }
}
