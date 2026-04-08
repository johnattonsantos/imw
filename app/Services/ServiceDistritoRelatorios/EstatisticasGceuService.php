<?php

namespace App\Services\ServiceDistritoRelatorios;

use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class EstatisticasGceuService
{
    public function execute(): array
    {
        $distrito = Identifiable::fetchtSessionDistrito();

        $subGceus = DB::table('gceu_cadastros as gc')
            ->select('gc.instituicao_id as igreja_id', DB::raw('COUNT(DISTINCT gc.id) as qtd_gceus'))
            ->where('gc.status', 'A')
            ->groupBy('gc.instituicao_id');

        $subMembros = DB::table('gceu_cadastros as gc')
            ->leftJoin('gceu_membros as gm', 'gm.gceu_cadastro_id', '=', 'gc.id')
            ->select('gc.instituicao_id as igreja_id', DB::raw('COUNT(DISTINCT gm.id) as qtd_membros_gceu'))
            ->where('gc.status', 'A')
            ->groupBy('gc.instituicao_id');

        $dados = DB::table('instituicoes_instituicoes as igreja')
            ->leftJoinSub($subGceus, 'sg', function ($join) {
                $join->on('sg.igreja_id', '=', 'igreja.id');
            })
            ->leftJoinSub($subMembros, 'sm', function ($join) {
                $join->on('sm.igreja_id', '=', 'igreja.id');
            })
            ->where('igreja.instituicao_pai_id', $distrito->id)
            ->where('igreja.tipo_instituicao_id', 1)
            ->select(
                'igreja.nome as igreja',
                DB::raw('COALESCE(sg.qtd_gceus, 0) as qtd_gceus'),
                DB::raw('COALESCE(sm.qtd_membros_gceu, 0) as qtd_membros_gceu')
            )
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
