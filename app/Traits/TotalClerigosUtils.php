<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait TotalClerigosUtils
{

    public static function fetchTotalClerigosStatus($regiaoId)
    {
        $results = DB::table('pessoas_pessoas as pp')
            ->rightJoin('pessoas_status as ps', 'pp.status_id', '=', 'ps.id')
            ->whereNull('pp.deleted_at')
            ->where('pp.regiao_id', '=', $regiaoId)
            ->select(DB::raw('count(*) as total'), 'ps.descricao')
            ->groupBy('pp.status_id', 'ps.descricao')
            ->orderByDesc('total')
            ->get();

        return self::somaPorcentual($results);
    }

    public static function fetchTotalClerigosNomeacoes($regiaoId)
    {
        $results = DB::table('pessoas_funcaoministerial as pf')
            ->select(DB::raw('COUNT(*) as total'), 'pf.funcao')
            ->leftJoin('pessoas_nomeacoes as pn', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->leftJoin('pessoas_pessoas as pp', function ($join) use ($regiaoId) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->where('pp.regiao_id', '=', $regiaoId);
            })
            ->whereNull('pn.deleted_at')
            ->where('pp.regiao_id', '=', $regiaoId)
            ->whereNull('pn.data_termino')
            ->groupBy('pf.funcao')
            ->orderByDesc('total')
            ->get();


        return self::somaPorcentual($results);
    }
    public static function fetchTotalClerigosFaxiaEtaria($regiaoId)
    {

        $results = DB::table('pessoas_pessoas as pp')
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('FLOOR(DATEDIFF(CURDATE(), pp.data_nascimento) / 365.25) as idade')
            )->where('pp.status_id', 1)
            ->where('pp.regiao_id', '=', $regiaoId)
            ->groupBy('pp.data_nascimento')
            ->orderByDesc('total')
            ->get();

        $faixasEtarias = [
            '20-29' => 0,
            '30-39' => 0,
            '40-49' => 0,
            '50-59' => 0,
            '60-69' => 0,
            '70+' => 0
        ];


        foreach ($results as $result) {
            if ($result->idade >= 20 && $result->idade < 30) {
                $faixasEtarias['20-29'] += $result->total;
            } elseif ($result->idade >= 30 && $result->idade < 40) {
                $faixasEtarias['30-39'] += $result->total;
            } elseif ($result->idade >= 40 && $result->idade < 50) {
                $faixasEtarias['40-49'] += $result->total;
            } elseif ($result->idade >= 50 && $result->idade < 60) {
                $faixasEtarias['50-59'] += $result->total;
            } elseif ($result->idade >= 60 && $result->idade < 70) {
                $faixasEtarias['60-69'] += $result->total;
            } elseif ($result->idade >= 70) {
                $faixasEtarias['70+'] += $result->total;
            }
        }


        $totalPessoas = array_sum($faixasEtarias);

        $faixasComPercentual = collect($faixasEtarias)->map(function ($total, $faixa) use ($totalPessoas) {
            $percentual = ($totalPessoas > 0) ? ($total * 100) / $totalPessoas : 0;
            return [
                'faixa' => $faixa,
                'total' => $total,
                'percentual' => round($percentual, 2)
            ];
        });

        return $faixasComPercentual;
    }



    public static function fetchTotalClerigosTipoVinculo($regiaoId)
    {
        $results = DB::table('pessoas_nomeacoes as pn')
            ->join('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->leftJoin('pessoas_pessoas as pp', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->where('pp.status_id', '=', 1);  // Condição do status na junção
            })
            ->whereNull('pn.deleted_at')
            ->whereNull('pn.data_termino')
            ->select(DB::raw('COUNT(*) as total'), 'pf.onus')
            ->groupBy('pf.onus')
            ->get();



        return self::somaPorcentual($results);
    }


    private static function somaPorcentual($results)
    {

        $total = $results->sum('total');
        $totalPorcentagem = $results->map(function ($result) use ($total) {
            $result->percentual = ($total > 0) ? ($result->total * 100) / $total : 0;
            return $result;
        });
        return $totalPorcentagem;
    }
}
