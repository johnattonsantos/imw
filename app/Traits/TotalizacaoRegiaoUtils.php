<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait TotalizacaoRegiaoUtils
{

    public static function fetchTotalDistroRegiao($regiaoId)
    {
        $result = DB::table('instituicoes_instituicoes as ii')
            ->join('instituicoes_instituicoes as ip', 'ii.instituicao_pai_id', '=', 'ip.id')
            ->selectRaw('COUNT(*) as total, ip.nome')
            ->where('ip.tipo_instituicao_id', InstituicoesTipoInstituicao::REGIAO)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('ip.id', $regiaoId)
            ->where('ii.ativo', 1)
            ->groupBy('ii.instituicao_pai_id', 'ip.nome')
            ->get();


        return $result;
    }

    public static function fetchTotalIgrejasPorDistrito($regiaoId)
    {
        $result = DB::table('instituicoes_instituicoes as ii')
            ->join('instituicoes_instituicoes as ip', 'ii.instituicao_pai_id', '=', 'ip.id')
            ->selectRaw('COUNT(*) as total, ip.nome')
            ->where('ip.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_GERAL)
            ->orWhere('ii.tipo_instituicao_id', operator: InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('ip.regiao_id', $regiaoId)
            ->where('ii.ativo', 1)
            ->groupBy('ii.instituicao_pai_id', 'ip.nome')
            ->orderByDesc('total')
            ->get();
        $total = $result->sum('total');
        $totalPorcentagem = $result->map(function ($distrito) use ($total) {
            $distrito->percentual = ($total > 0) ? ($distrito->total * 100) / $total : 0;
            return $distrito;
        });
        return $totalPorcentagem;
    }
    public static function fetchTotalCongregacoesPorIgrejas($regiaoId)
    {
        $result = DB::table('instituicoes_instituicoes as ii')
            ->join('congregacoes_congregacoes as cc', 'ii.id', '=', 'cc.instituicao_id')
            ->selectRaw('COUNT(*) as total, ii.nome')
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('ii.regiao_id', $regiaoId)
            ->where('ii.ativo', 1)
            ->where('cc.ativo', 1)
            ->groupBy('ii.instituicao_pai_id', 'ii.nome')
            ->orderByDesc('total')
            ->get();

        $total = $result->sum('total');
        $totalPorcentagem = $result->map(function ($igrejas) use ($total) {
            $igrejas->percentual = ($total > 0) ? ($igrejas->total * 100) / $total : 0;
            return $igrejas;
        });
        return $totalPorcentagem;
    }

    public static function fetchTotalCongregacoesPorDistritos($regiaoId)
    {
        $result = DB::table('instituicoes_instituicoes as ii')
            ->join('congregacoes_congregacoes as cc', 'ii.id', '=', 'cc.instituicao_id')
            ->join('instituicoes_instituicoes as ip', 'ip.id', '=', 'ii.instituicao_pai_id')
            ->selectRaw('COUNT(*) as total, ip.nome')
            ->where('ip.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('ii.ativo', 1)
            ->where('ip.regiao_id', $regiaoId)
            ->where('cc.ativo', 1)
            ->groupBy('ii.instituicao_pai_id', 'ip.nome')
            ->orderByDesc('total')
            ->get();
        $total = $result->sum('total');
        $totalPorcentagem = $result->map(function ($distritos) use ($total) {
            $distritos->percentual = ($total > 0) ? ($distritos->total * 100) / $total : 0;
            return $distritos;
        });
        return $totalPorcentagem;
    }


    public static function fetchDezDistritoBatismo($dataFinal, $dataInicial, $regiaoId)
    {
        $instituicoes = DB::table('instituicoes_instituicoes as ii')
            ->leftJoin('membresia_rolpermanente as mr', function ($join) use ($dataInicial, $dataFinal) {
                $join->on('mr.distrito_id', '=', 'ii.id')
                    ->where('mr.modo_recepcao_id', 1)
                    ->whereBetween('mr.dt_recepcao', [$dataInicial, $dataFinal]);
            })
            ->selectRaw('COUNT(DISTINCT mr.membro_id) as total, ii.nome')
            ->where('ii.regiao_id', $regiaoId)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->groupBy('ii.id', 'ii.nome')
            ->orderByDesc('total', 'DESC')
            ->limit(10)
            ->get();
            // ->from('instituicoes_instituicoes as ii')
            // ->join('membresia_membros as mm', 'mm.distrito_id', '=', 'ii.id')
            // ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            // ->where('mm.status', 'A')
            // ->where('ii.ativo', 1)
            // ->whereBetween('mm.data_batismo', [$dataInicial, $dataFinal])
            // ->groupBy('mm.distrito_id', 'ii.nome')
            // ->orderByDesc('total')
            // ->limit(10)
            // ->get();

        $total = $instituicoes->sum('total');
        $totalPorcentagem = $instituicoes->map(function ($instituicao) use ($total) {
            $instituicao->percentual = ($total > 0) ? ($instituicao->total * 100) / $total : 0;
            return $instituicao;
        });

        return $totalPorcentagem;
    }
    public static function fetchDezDistritoMembros($dataFinal, $dataInicial, $regiaoId)
    {

        $instituicoes = DB::table('instituicoes_instituicoes as ii')
            ->selectRaw('COUNT(DISTINCT mm.id) as total, ii.nome')
            ->leftJoin('membresia_membros as mm', function ($join) use ($dataInicial, $dataFinal) {
                $join->on('mm.distrito_id', '=', 'ii.id')
                    ->where('mm.status', 'A')
                    ->where('mm.vinculo', 'M')
                    ->whereBetween('mm.created_at', [$dataInicial, $dataFinal]);
            })
            ->where('ii.regiao_id', $regiaoId)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->groupBy('ii.id', 'ii.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $total = $instituicoes->sum('total');
        $totalPorcentagem = $instituicoes->map(function ($instituicao) use ($total) {
            $instituicao->percentual = ($total > 0) ? ($instituicao->total * 100) / $total : 0;
            return $instituicao;
        });

        return $totalPorcentagem;
    }
    public static function fetchDezDistritoCresceramMembros($dataFinal, $dataInicial, $regiaoId)
    {
        $instituicoes = DB::table('instituicoes_instituicoes as ii')
            ->leftJoin('membresia_rolpermanente as mr', function ($join) use ($dataInicial, $dataFinal) {
                $join->on('mr.distrito_id', '=', 'ii.id')
                    ->whereIn('mr.modo_recepcao_id', [1, 2, 3]) // Batismo, Adesão, Reconciliação
                    ->whereBetween('mr.dt_recepcao', [$dataInicial, $dataFinal]);
            })
            ->selectRaw('COUNT(DISTINCT mr.membro_id) as total, ii.nome')
            ->where('ii.regiao_id', $regiaoId)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->groupBy('ii.id', 'ii.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $total = $instituicoes->sum('total');
        $totalPorcentagem = $instituicoes->map(function ($instituicao) use ($total) {
            $instituicao->percentual = ($total > 0) ? ($instituicao->total * 100) / $total : 0;
            return $instituicao;
        });

        return $totalPorcentagem;
    }




    public static function fetchDezIgrejaBatismo($dataFinal, $dataInicial, $regiaoId)
    {
        $instituicoes = DB::table('instituicoes_instituicoes as ii')
            ->leftJoin('membresia_rolpermanente as mr', function ($join) use ($dataInicial, $dataFinal) {
                $join->on('mr.igreja_id', '=', 'ii.id')
                    ->where('mr.modo_recepcao_id', 1)
                    ->whereBetween('mr.dt_recepcao', [$dataInicial, $dataFinal]);
            })
            ->selectRaw('COUNT(DISTINCT mr.membro_id) as total, ii.nome')
            ->where('ii.regiao_id', $regiaoId)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->groupBy('ii.id', 'ii.nome')
            ->orderByDesc('total', 'DESC')
            ->limit(10)
            ->get();
            // ->from('instituicoes_instituicoes as ii')
            // ->leftJoin('membresia_membros as mm', function ($join) use ($dataInicial, $dataFinal)  {
            //     $join->on('mm.igreja_id', '=', 'ii.id')
            //         ->where('mm.status', 'A')
            //         ->whereBetween('mm.data_batismo', [$dataInicial, $dataFinal]);
            // })
            // ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            // ->where('ii.ativo', 1)
            // ->groupBy('ii.id', 'ii.nome')
            // ->orderByDesc('total')
            // ->limit(10)
            // ->get();

        $total = $instituicoes->sum('total');
        $totalPorcentagem = $instituicoes->map(function ($instituicao) use ($total) {
            $instituicao->percentual = ($total > 0) ? ($instituicao->total * 100) / $total : 0;
            return $instituicao;
        });

        return $totalPorcentagem;
    }
    public static function fetchDezIgrejaMembros($dataFinal, $dataInicial, $regiaoId)
    {

        $instituicoes = DB::table('instituicoes_instituicoes as ii')
            ->selectRaw('COUNT(mm.id) as total, ii.nome')
            ->leftJoin('membresia_membros as mm', function ($join) use ($dataInicial, $dataFinal) {
                $join->on('mm.igreja_id', '=', 'ii.id')
                    ->where('mm.status', 'A')
                    ->whereBetween('mm.created_at', [$dataInicial, $dataFinal]);
            })
            ->where('ii.regiao_id', $regiaoId)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->groupBy('ii.id', 'ii.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $total = $instituicoes->sum('total');
        $totalPorcentagem = $instituicoes->map(function ($instituicao) use ($total) {
            $instituicao->percentual = ($total > 0) ? ($instituicao->total * 100) / $total : 0;
            return $instituicao;
        });

        return $totalPorcentagem;
    }
    public static function fetchDezIgrejaCresceramMembros($dataFinal, $dataInicial, $regiaoId)
    {
        $instituicoes = DB::table('instituicoes_instituicoes as ii')
            ->leftJoin('membresia_rolpermanente as mr', function ($join) use ($dataInicial, $dataFinal) {
                $join->on('mr.igreja_id', '=', 'ii.id')
                    ->whereIn('mr.modo_recepcao_id', [1, 2, 3]) // Batismo, Adesão, Reconciliação
                    ->whereBetween('mr.dt_recepcao', [$dataInicial, $dataFinal]);
            })
            ->selectRaw('COUNT(DISTINCT mr.membro_id) as total, ii.nome')
            ->where('ii.regiao_id', $regiaoId)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->groupBy('ii.id', 'ii.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $total = $instituicoes->sum('total');
        $totalPorcentagem = $instituicoes->map(function ($instituicao) use ($total) {
            $instituicao->percentual = ($total > 0) ? ($instituicao->total * 100) / $total : 0;
            return $instituicao;
        });

        return $totalPorcentagem;
    }
    public static function fetchFrenteMissionaria($instituicao)
    {


        $results = DB::table('instituicoes_instituicoes as ii')
            ->join('instituicoes_instituicoes as ip', function ($join) use($instituicao) {
                $join->on('ip.id', '=', 'ii.instituicao_pai_id')
                    ->where('ip.tipo_instituicao_id', $instituicao); // Instituição pai tipo 2
            })
            ->where('ii.nome', 'LIKE', '%Frente Missionária%') // Filtro pelo nome
            ->where('ii.tipo_instituicao_id', 1) // Filtro para instituição filha tipo 1
            ->select(DB::raw('COUNT(*) as total'), 'ip.nome') // Contagem e seleção do nome da instituição pai
            ->groupBy('ip.nome') // Agrupar pelo nome da instituição pai
            ->orderByDesc(DB::raw('total')) // Ordenar pela contagem de forma decrescente
            ->get(); // Obter o resultsado

        // Exibir o resultsado


        $total = $results->sum('total');
        $totalPorcentagem = $results->map(function ($instituicao) use ($total) {
            $instituicao->percentual = ($total > 0) ? ($instituicao->total * 100) / $total : 0;
            return $instituicao;
        });

        return $totalPorcentagem;
    }
}
