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
            ->join('pessoas_status as ps', 'pp.status_id', '=', 'ps.id')
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->whereNull('pn.data_termino');
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')
                    ->where('ii.ativo', '=', 1)
                    ->whereNull('ii.data_encerramento');
            })
            ->leftJoin('instituicoes_instituicoes as ip', 'ip.id', '=', 'ii.instituicao_pai_id')
            ->whereNull('pn.deleted_at')
            ->where('pp.regiao_id', '=', $regiaoId)
            ->where(function ($query) {
                $query->whereNull('ip.id')
                    ->orWhere(function ($parentQuery) {
                        $parentQuery->where('ip.ativo', 1)
                            ->whereNull('ip.data_encerramento');
                    });
            })
            ->select(DB::raw('COUNT(DISTINCT pp.id) as total'), 'ps.descricao')
            ->groupBy('pp.status_id', 'ps.descricao')
            ->orderByDesc('total')
            ->get();

        return self::somaPorcentual($results);
    }

    public static function fetchTotalClerigosNomeacoes($regiaoId)
    {
        $results = DB::table('pessoas_nomeacoes as pn')
            ->join('pessoas_funcaoministerial as pf', 'pf.id', '=', 'pn.funcao_ministerial_id')
            ->join('pessoas_pessoas as pp', 'pp.id', '=', 'pn.pessoa_id')
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')
                    ->where('ii.ativo', '=', 1)
                    ->whereNull('ii.data_encerramento');
            })
            ->leftJoin('instituicoes_instituicoes as ip', 'ip.id', '=', 'ii.instituicao_pai_id')
            ->select(DB::raw('COUNT(DISTINCT pp.id) as total'), 'pf.funcao')
            ->whereNull('pn.deleted_at')
            ->where('pp.regiao_id', '=', $regiaoId)
            ->whereNull('pn.data_termino')
            ->where(function ($query) {
                $query->whereNull('ip.id')
                    ->orWhere(function ($parentQuery) {
                        $parentQuery->where('ip.ativo', 1)
                            ->whereNull('ip.data_encerramento');
                    });
            })
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
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('pessoas_nomeacoes as pn')
                    ->join('instituicoes_instituicoes as ii', function ($join) {
                        $join->on('pn.instituicao_id', '=', 'ii.id')
                            ->where('ii.ativo', '=', 1)
                            ->whereNull('ii.data_encerramento');
                    })
                    ->leftJoin('instituicoes_instituicoes as ip', 'ip.id', '=', 'ii.instituicao_pai_id')
                    ->whereColumn('pn.pessoa_id', 'pp.id')
                    ->whereNull('pn.deleted_at')
                    ->whereNull('pn.data_termino')
                    ->where(function ($parentQuery) {
                        $parentQuery->whereNull('ip.id')
                            ->orWhere(function ($activeParentQuery) {
                                $activeParentQuery->where('ip.ativo', 1)
                                    ->whereNull('ip.data_encerramento');
                            });
                    });
            })
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
            ->join('pessoas_pessoas as pp', function ($join) use ($regiaoId) {
                $join->on('pp.id', '=', 'pn.pessoa_id')
                    ->where('pp.status_id', '=', 1)
                    ->where('pp.regiao_id', '=', $regiaoId);
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')
                    ->where('ii.ativo', '=', 1)
                    ->whereNull('ii.data_encerramento');
            })
            ->leftJoin('instituicoes_instituicoes as ip', 'ip.id', '=', 'ii.instituicao_pai_id')
            ->whereNull('pn.deleted_at')
            ->whereNull('pn.data_termino')
            ->where(function ($query) {
                $query->whereNull('ip.id')
                    ->orWhere(function ($parentQuery) {
                        $parentQuery->where('ip.ativo', 1)
                            ->whereNull('ip.data_encerramento');
                    });
            })
            ->select(DB::raw('COUNT(DISTINCT pp.id) as total'), 'pf.onus')
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
