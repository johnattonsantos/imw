<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait EstatisticaGeneroUtils
{
    public static function fetch($dataInicial, $dataFinal, $tipo, $distritoId, $regiaoId = null): Collection
    {
        $results = DB::table('membresia_rolpermanente as mr')
            ->join('membresia_membros as mm', 'mm.id', '=', 'mr.membro_id')
            ->join('instituicoes_instituicoes as ii', 'ii.id', '=', 'mr.igreja_id')
            ->select('ii.id', 'ii.nome', 'dist.nome as distrito')
            ->selectRaw(
                'COUNT(CASE WHEN mr.dt_recepcao <= ? AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > ?) THEN mr.id END) AS total_x',
                [$dataInicial, $dataInicial]
            )
            ->selectRaw(
                'COUNT(CASE WHEN mr.dt_recepcao <= ? AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > ?) THEN mr.id END) AS total_y',
                [$dataFinal, $dataFinal]
            )
            ->selectRaw(
                "COUNT(CASE WHEN mr.dt_recepcao <= ? AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > ?) AND mm.sexo = 'M' THEN mr.id END) AS total_masculino_x",
                [$dataInicial, $dataInicial]
            )
            ->selectRaw(
                "COUNT(CASE WHEN mr.dt_recepcao <= ? AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > ?) AND mm.sexo = 'M' THEN mr.id END) AS total_masculino_y",
                [$dataFinal, $dataFinal]
            )
            ->selectRaw(
                "COUNT(CASE WHEN mr.dt_recepcao <= ? AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > ?) AND mm.sexo = 'F' THEN mr.id END) AS total_feminino_x",
                [$dataInicial, $dataInicial]
            )
            ->selectRaw(
                "COUNT(CASE WHEN mr.dt_recepcao <= ? AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > ?) AND mm.sexo = 'F' THEN mr.id END) AS total_feminino_y",
                [$dataFinal, $dataFinal]
            )
            ->leftJoin('instituicoes_instituicoes as dist', function ($join) {
                $join->on('ii.instituicao_pai_id', '=', 'dist.id')
                    ->where('dist.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO);
            })
            ->where('ii.tipo_instituicao_id', 1)
            ->when(
                $distritoId == 'all' && $regiaoId,
                fn ($query) => $query->whereIn(
                    'ii.instituicao_pai_id',
                    Identifiable::fetchDistritosIdByRegiao($regiaoId)
                ),
                fn ($query) => $query->where('ii.instituicao_pai_id', $distritoId)
            )
            ->groupBy('ii.id', 'ii.nome', 'dist.nome')
            ->havingRaw('total_x > 0 OR total_y > 0')
            ->orderBy('dist.nome', 'asc')
            ->orderBy('ii.nome', 'asc')
            ->get();

        return $results;
    }
}
