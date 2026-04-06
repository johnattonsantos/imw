<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait EstatisticaGeneroUtils
{
    public static function fetch($dataInicial, $dataFinal, $tipo, $distritoId, $regiaoId = null): Collection
    {
      $vinculoCondition = $tipo === 'C' ? ['C', 'M'] : ['M'];

      $results = DB::table('instituicoes_instituicoes as ii')
        ->select('ii.id', 'ii.nome', 'dist.nome as distrito')
        // Total de membros até dataInicial (total_x)
        ->selectSub(function ($query) use ($dataInicial, $vinculoCondition) {
            $query->from('membresia_membros as mm')
                ->join('membresia_rolpermanente as mr', 'mr.membro_id', '=', 'mm.id')
 //               ->whereColumn('mr.distrito_id', 'ii.instituicao_pai_id')
                ->where('mm.status', 'A')
                ->whereIn('mm.vinculo', $vinculoCondition)
                ->whereColumn('mr.igreja_id', 'ii.id')
                ->where('mr.dt_recepcao', '<=', $dataInicial)
                ->where(function ($query) use ($dataInicial) {
                    $query->where('mr.dt_exclusao', '>', $dataInicial)
                        ->orWhereNull('mr.dt_exclusao');
                })
                ->selectRaw('COUNT(*)');
        }, 'total_x')
        // Total de membros até dataFinal (total_y)
        ->selectSub(function ($query) use ($dataFinal, $vinculoCondition) {
            $query->from('membresia_membros as mm')
                ->join('membresia_rolpermanente as mr', 'mr.membro_id', '=', 'mm.id')
 //               ->whereColumn('mr.distrito_id', 'ii.instituicao_pai_id')
                ->where('mm.status', 'A')
                ->whereIn('mm.vinculo', $vinculoCondition)
                ->whereColumn('mr.igreja_id', 'ii.id')
                ->where('mr.dt_recepcao', '<=', $dataFinal)
                ->where(function ($query) use ($dataFinal) {
                    $query->where('mr.dt_exclusao', '>', $dataFinal)
                        ->orWhereNull('mr.dt_exclusao');
                })
                ->selectRaw('COUNT(*)');
        }, 'total_y')
        // Total de membros masculinos até dataInicial (total_masculino_x)
        ->selectSub(function ($query) use ($dataInicial, $vinculoCondition) {
            $query->from('membresia_membros as mm')
                ->join('membresia_rolpermanente as mr', 'mr.membro_id', '=', 'mm.id')
//                ->whereColumn('mr.distrito_id', 'ii.instituicao_pai_id')
                ->where('mm.status', 'A')
                ->whereIn('mm.vinculo', $vinculoCondition)
                ->whereColumn('mr.igreja_id', 'ii.id')
                ->where('mr.dt_recepcao', '<=', $dataInicial)
                ->where(function ($query) use ($dataInicial) {
                    $query->where('mr.dt_exclusao', '>', $dataInicial)
                        ->orWhereNull('mr.dt_exclusao');
                })
                ->where('mm.sexo', 'M')
                ->selectRaw('COUNT(*)');
        }, 'total_masculino_x')
        // Total de membros masculinos até dataFinal (total_masculino_y)
        ->selectSub(function ($query) use ($dataFinal, $vinculoCondition) {
            $query->from('membresia_membros as mm')
                ->join('membresia_rolpermanente as mr', 'mr.membro_id', '=', 'mm.id')
//                ->whereColumn('mr.distrito_id', 'ii.instituicao_pai_id')
                ->where('mm.status', 'A')
                ->whereIn('mm.vinculo', $vinculoCondition)
                ->whereColumn('mr.igreja_id', 'ii.id')
                ->where('mr.dt_recepcao', '<=', $dataFinal)
                ->where(function ($query) use ($dataFinal) {
                    $query->where('mr.dt_exclusao', '>', $dataFinal)
                        ->orWhereNull('mr.dt_exclusao');
                })
                ->where('mm.sexo', 'M')
                ->selectRaw('COUNT(*)');
        }, 'total_masculino_y')
        // Total de membros femininos até dataInicial (total_feminino_x)
        ->selectSub(function ($query) use ($dataInicial, $vinculoCondition) {
            $query->from('membresia_membros as mm')
                ->join('membresia_rolpermanente as mr', 'mr.membro_id', '=', 'mm.id')
//                ->whereColumn('mr.distrito_id', 'ii.instituicao_pai_id')
                ->where('mm.status', 'A')
                ->whereIn('mm.vinculo', $vinculoCondition)
                ->whereColumn('mr.igreja_id', 'ii.id')
                ->where('mr.dt_recepcao', '<=', $dataInicial)
                ->where(function ($query) use ($dataInicial) {
                    $query->where('mr.dt_exclusao', '>', $dataInicial)
                        ->orWhereNull('mr.dt_exclusao');
                })
                ->where('mm.sexo', 'F')
                ->selectRaw('COUNT(*)');
        }, 'total_feminino_x')
        // Total de membros femininos até dataFinal (total_feminino_y)
        ->selectSub(function ($query) use ($dataFinal, $vinculoCondition) {
            $query->from('membresia_membros as mm')
                ->join('membresia_rolpermanente as mr', 'mr.membro_id', '=', 'mm.id')
//                ->whereColumn('mr.distrito_id', 'ii.instituicao_pai_id')
                ->where('mm.status', 'A')
                ->whereIn('mm.vinculo', $vinculoCondition)
                ->whereColumn('mr.igreja_id', 'ii.id')
                ->where('mr.dt_recepcao', '<=', $dataFinal)
                ->where(function ($query) use ($dataFinal) {
                    $query->where('mr.dt_exclusao', '>', $dataFinal)
                        ->orWhereNull('mr.dt_exclusao');
                })
                ->where('mm.sexo', 'F')
                ->selectRaw('COUNT(*)');
        }, 'total_feminino_y')
        ->leftJoin('instituicoes_instituicoes as dist', function ($join) {
            $join->on('ii.instituicao_pai_id', '=', 'dist.id')
                ->where('dist.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO);
        })
        ->when($distritoId == 'all' && $regiaoId,
            fn ($query) => $query->whereIn('ii.instituicao_pai_id', Identifiable::fetchDistritosIdByRegiao($regiaoId)),
            fn ($query) => $query->where('ii.instituicao_pai_id', $distritoId)
        )
        ->orderBy('dist.nome')
        ->orderBy('ii.nome')
        ->get();
        return $results;
    }
}
