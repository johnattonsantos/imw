<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Facades\DB;

trait MembrosMinisterioUtils
{
    public static function fetch($dataInicial, $dataFinal, $tipo, $distritoId, $regiaoId = null)
    {
        $faixasEtarias = [
            'Kids' => ['operator' => 'BETWEEN ? AND ?', 'bounds' => [0, 9]],
            'Conexao' => ['operator' => 'BETWEEN ? AND ?', 'bounds' => [10, 13]],
            'Fire' => ['operator' => 'BETWEEN ? AND ?', 'bounds' => [14, 17]],
            'Move' => ['operator' => 'BETWEEN ? AND ?', 'bounds' => [18, 29]],
            'Homens' => ['operator' => 'BETWEEN ? AND ?', 'bounds' => [30, 59], 'sexo' => 'M'],
            'Mulheres' => ['operator' => 'BETWEEN ? AND ?', 'bounds' => [30, 59], 'sexo' => 'F'],
            'Sessenta' => ['operator' => '>= ?', 'bounds' => [60]],
            'Homens30' => ['operator' => '>= ?', 'bounds' => [30], 'sexo' => 'M'],
            'Mulheres30' => ['operator' => '>= ?', 'bounds' => [30], 'sexo' => 'F'],
        ];

        $datas = [
            'X' => $dataInicial,
            'Y' => $dataFinal,
        ];

        $results = DB::table('membresia_rolpermanente as mr')
            ->join('membresia_membros as mm', 'mm.id', '=', 'mr.membro_id')
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('ii.id', '=', 'mr.igreja_id')
                    ->where('ii.tipo_instituicao_id', 1);
            })
            ->select('ii.id as igreja_id', 'ii.nome', 'dist.id as distrito_id', 'dist.nome as distrito');

        foreach ($datas as $sufixo => $data) {
            foreach ($faixasEtarias as $faixa => $condicao) {
                $sql = 'COUNT(CASE WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, ?) '
                    .$condicao['operator'].' AND mr.dt_recepcao < ? '
                    .'AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao >= ?)';
                $bindings = array_merge([$data], $condicao['bounds'], [$data, $data]);

                if (isset($condicao['sexo'])) {
                    $sql .= ' AND mm.sexo = ?';
                    $bindings[] = $condicao['sexo'];
                }

                $sql .= ' THEN mr.id END) AS `'.$faixa.'_'.$sufixo.'`';

                $results->selectRaw($sql, $bindings);
            }
        }

        $results
            ->leftJoin('instituicoes_instituicoes as dist', function ($join) {
                $join->on('ii.instituicao_pai_id', '=', 'dist.id')
                    ->where('dist.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
                    ->where('dist.ativo', 1);
            })
            ->when(
                $distritoId == 'all' && $regiaoId,
                fn ($query) => $query->whereIn(
                    'ii.instituicao_pai_id',
                    DB::table('instituicoes_instituicoes')
                        ->select('id')
                        ->where('instituicao_pai_id', $regiaoId)
                        ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
                        ->whereNull('data_encerramento')
                ),
                fn ($query) => $query->where('ii.instituicao_pai_id', $distritoId)
            )
            ->groupBy('ii.id', 'ii.nome', 'dist.id', 'dist.nome')
            ->orderBy('dist.nome', 'asc')
            ->orderBy('ii.nome', 'asc');

        $results = $results->get();

        foreach ($results as $row) {
            // Keep totals based on disjoint age groups: seniors also appear in the new 30+ readings.
            foreach (['X', 'Y'] as $sufixo) {
                $row->{'Total_'.$sufixo} = $row->{'Kids_'.$sufixo} + $row->{'Conexao_'.$sufixo}
                    + $row->{'Fire_'.$sufixo} + $row->{'Move_'.$sufixo} + $row->{'Homens_'.$sufixo}
                    + $row->{'Mulheres_'.$sufixo} + $row->{'Sessenta_'.$sufixo};
            }
        }

        return $results;
    }
}
