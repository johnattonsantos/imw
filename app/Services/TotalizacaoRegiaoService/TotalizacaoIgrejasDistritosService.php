<?php

namespace App\Services\TotalizacaoRegiaoService;


use App\Traits\Identifiable;
use App\Traits\TotalizacaoRegiaoUtils;


class TotalizacaoIgrejasDistritosService
{
    use Identifiable;

    public function execute(?int $distritoId = null)
    {


        $regiao = Identifiable::fetchtSessionRegiao();
        $igrejasDetalhadas = TotalizacaoRegiaoUtils::fetchIgrejasDetalhadasPorDistrito($regiao->id, $distritoId);
        $igrejasPorDistrito = $igrejasDetalhadas
            ->groupBy('distrito_id')
            ->map(function ($linhas) {
                $primeiraLinha = $linhas->first();
                $igrejas = $linhas->filter(fn ($linha) => !empty($linha->igreja_id))->values();

                return (object) [
                    'distrito_id' => $primeiraLinha->distrito_id,
                    'distrito_nome' => $primeiraLinha->distrito_nome,
                    'igrejas' => $igrejas,
                    'total' => $igrejas->count(),
                ];
            })
            ->values();

        return [
            'lancamentos' => TotalizacaoRegiaoUtils::fetchTotalIgrejasPorDistrito($regiao->id),
            'igrejasPorDistrito' => $igrejasPorDistrito,
            'totalIgrejasRegiao' => $igrejasPorDistrito->sum('total'),
            'distritoSelecionado' => $distritoId,
            'distritos'   => Identifiable::fetchDistritosByRegiao($regiao->id),
            'regiao'      => $regiao
        ];
    }
}
