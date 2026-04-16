<?php
namespace App\Services\ServiceEstatisticas;

use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class TotalMembresiaServices
{
    public function execute($tipo)
    {
        $regiao_id = Identifiable::fetchtSessionRegiao()->id;

        if ($tipo == 'igreja') {
            // Consulta para igrejas agrupadas por distrito
            $resultado = DB::table('instituicoes_instituicoes as iilocal')
                ->leftJoin('membresia_membros as mm', function ($join) use ($regiao_id) {
                    $join->on('iilocal.id', '=', 'mm.igreja_id')
                        ->where('mm.status', '=', 'A')
                        ->where('mm.vinculo', '=', 'M')
                        ->where('mm.regiao_id', '=', $regiao_id);
                })
                ->leftJoin('instituicoes_instituicoes as iipai', 'iipai.id', '=', 'iilocal.instituicao_pai_id')
                ->whereIn('iilocal.instituicao_pai_id', function ($query) use ($regiao_id) {
                    $query->select('ii.id')
                        ->from('instituicoes_instituicoes as ii')
                        ->where('ii.regiao_id', $regiao_id)
                        ->where('ii.tipo_instituicao_id', 2)
                        ->whereNull('ii.deleted_at')
                        ->whereNull('ii.data_encerramento');
                })
                ->whereNull('iipai.data_encerramento')
                ->select(
                    'iipai.id as distrito_id',
                    'iipai.nome as distrito_nome',
                    'iilocal.id as igreja_id',
                    'iilocal.nome as igreja_nome',
                    DB::raw('COUNT(mm.id) AS total_membros')
                )
                ->groupBy('iipai.id', 'iipai.nome', 'iilocal.id', 'iilocal.nome')
                ->orderBy('iipai.nome')
                ->orderBy('iilocal.nome')
                ->get();
        } else {
            // Consulta para distritos
            $resultado = DB::table('instituicoes_instituicoes as ii')
                ->leftJoin('membresia_membros as mm', function ($join) use ($regiao_id) {
                    $join->on('ii.id', '=', 'mm.distrito_id')
                        ->where('mm.status', '=', 'A')
                        ->where('mm.vinculo', '=', 'M')
                        ->where('mm.regiao_id', '=', $regiao_id);
                })
                ->where('ii.regiao_id', $regiao_id)
                ->where('ii.tipo_instituicao_id', 2)
                ->whereNull('ii.deleted_at')
                ->whereNull('ii.data_encerramento')
                ->select(
                    'ii.id as distrito_id',
                    'ii.nome as distrito_nome',
                    DB::raw('COUNT(mm.distrito_id) AS total_membros')
                )
                ->groupBy('ii.id', 'ii.nome')
                ->orderBy('ii.nome', 'asc')
                ->get();
        }

        // Calcula o total geral
        $totalGeral = $resultado->sum('total_membros');

        return [
            'regiao' => $regiao_id,
            'resultado' => $resultado,
            'totalGeral' => $totalGeral
        ];
    }
}
