<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class EstatisticasGceuService
{
    public function execute(): array
    {
        $regiao = Identifiable::fetchtSessionRegiao();

        $subGceus = DB::table('gceu_cadastros as gc')
            ->select('gc.instituicao_id as igreja_id', DB::raw('COUNT(DISTINCT gc.id) as qtd_gceus'))
            ->where('gc.status', 'A')
            ->groupBy('gc.instituicao_id');

        $subMembros = DB::table('gceu_cadastros as gc')
            ->leftJoin('gceu_membros as gm', 'gm.gceu_cadastro_id', '=', 'gc.id')
            ->select('gc.instituicao_id as igreja_id', DB::raw('COUNT(DISTINCT gm.id) as qtd_membros_gceu'))
            ->where('gc.status', 'A')
            ->groupBy('gc.instituicao_id');

        $dados = DB::table('instituicoes_instituicoes as distrito')
            ->join('instituicoes_instituicoes as igreja', function ($join) {
                $join->on('igreja.instituicao_pai_id', '=', 'distrito.id')
                    ->where('igreja.tipo_instituicao_id', 1);
            })
            ->leftJoinSub($subGceus, 'sg', function ($join) {
                $join->on('sg.igreja_id', '=', 'igreja.id');
            })
            ->leftJoinSub($subMembros, 'sm', function ($join) {
                $join->on('sm.igreja_id', '=', 'igreja.id');
            })
            ->where('distrito.instituicao_pai_id', $regiao->id)
            ->where('distrito.tipo_instituicao_id', 2)
            ->select(
                'distrito.nome as distrito',
                'igreja.nome as igreja',
                DB::raw('COALESCE(sg.qtd_gceus, 0) as qtd_gceus'),
                DB::raw('COALESCE(sm.qtd_membros_gceu, 0) as qtd_membros_gceu')
            )
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->get();

        return [
            'dados' => $dados,
            'totais' => [
                'qtd_gceus' => (int) $dados->sum('qtd_gceus'),
                'qtd_membros_gceu' => (int) $dados->sum('qtd_membros_gceu'),
            ],
        ];
    }
}
