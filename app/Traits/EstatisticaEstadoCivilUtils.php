<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait EstatisticaEstadoCivilUtils
{
    public static function fetch($distritoId, $regiaoId = null, $vinculo = 'M'): Collection
    {
        $estadosCivis = collect([
            (object) ['estado_civil' => 'S', 'descricao' => 'Solteiro'],
            (object) ['estado_civil' => 'C', 'descricao' => 'Casado'],
            (object) ['estado_civil' => 'V', 'descricao' => 'Viúvo'],
            (object) ['estado_civil' => 'D', 'descricao' => 'Divorciado'],
            (object) ['estado_civil' => 'N', 'descricao' => 'Não informado'],
        ]);

        $estadoCivilNormalizadoSql = "CASE
                WHEN mm.estado_civil IS NULL OR TRIM(mm.estado_civil) = '' THEN 'N'
                WHEN UPPER(TRIM(mm.estado_civil)) IN ('S', 'C', 'V', 'D') THEN UPPER(TRIM(mm.estado_civil))
                ELSE 'N'
            END";

        $baseQuery = DB::table('membresia_membros as mm')
            ->selectRaw("mm.id as membro_id, {$estadoCivilNormalizadoSql} as estado_civil")
            ->where('mm.vinculo', $vinculo);

        if ($regiaoId !== null) {
            $baseQuery->where('mm.regiao_id', $regiaoId);
        }

        if ($distritoId !== "all" && !empty($distritoId)) {
            $baseQuery->where('mm.distrito_id', $distritoId);
        }

        $result = DB::query()
            ->fromSub($baseQuery, 'base')
            ->selectRaw('COUNT(base.membro_id) as total, base.estado_civil')
            ->groupBy('base.estado_civil')
            ->get()
            ->keyBy('estado_civil');

        $finalResult = $estadosCivis->map(function ($estado) use ($result) {
            return (object) [
                'estado_civil' => $estado->descricao,
                'total' => $result->has($estado->estado_civil) ? $result[$estado->estado_civil]->total : 0
            ];
        });

        $totalGeral = $finalResult->sum('total');
        $finalResult = $finalResult->map(function ($item) use ($totalGeral) {
            $item->percentual = ($totalGeral > 0) ? ($item->total * 100) / $totalGeral : 0;
            return $item;
        });

        return $finalResult;
    }
}
