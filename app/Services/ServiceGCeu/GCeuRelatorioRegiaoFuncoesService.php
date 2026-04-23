<?php

namespace App\Services\ServiceGCeu;

use App\Models\GCeu;
use App\Models\GCeuFuncoes;
use Illuminate\Support\Facades\DB;

class GCeuRelatorioRegiaoFuncoesService
{
    public function getList($regiaoId, $distritoId, $igrejaId, $funcaoId, $gceuId, $tipo = null)
    {
        $dados = DB::table('instituicoes_instituicoes as igreja')
            ->select(
                'distrito.id',
                'distrito.nome as distrito_nome',
                'igreja.id as id_igreja',
                'igreja.nome as igreja_nome',
                'gceu.*',
                'membresia_membros.nome as lider', 
                'membresia_contatos.telefone_preferencial',
                'gceu_funcoes.funcao',
                DB::raw("CASE membresia_membros.vinculo
                            WHEN 'M' THEN 'Membro'
                            WHEN 'C' THEN 'Congregado'
                            WHEN 'V' THEN 'Visitante'
                            ELSE 'Não informado'
                        END as tipo")
            )->join('instituicoes_instituicoes as distrito', function ($join) {
                $join->on('distrito.id', '=', 'igreja.instituicao_pai_id');
            })->join('instituicoes_instituicoes as regiao', function ($join) {
                $join->on('regiao.id', '=', 'distrito.instituicao_pai_id');
            })
            ->join('gceu_cadastros as gceu', function ($join) {
                $join->on('gceu.instituicao_id', '=', 'igreja.id');
            })
            ->join('gceu_membros', 'gceu_membros.gceu_cadastro_id', 'gceu.id')
            ->join('membresia_membros', 'membresia_membros.id', 'gceu_membros.membro_id')
            ->join('gceu_funcoes', 'gceu_funcoes.id', 'gceu_membros.gceu_funcao_id')
            ->leftJoin('membresia_contatos', 'membresia_contatos.membro_id', 'membresia_membros.id')
            ->where(['regiao.id' => $regiaoId, 'gceu.status' => 'A'])
            ->when($funcaoId, function ($query) use ($funcaoId) {
                $query->where('gceu_membros.gceu_funcao_id', $funcaoId);
            })
            ->when($gceuId, function ($query) use ($gceuId) {
                $query->where('gceu.id', $gceuId);
            })
            ->when($tipo, function ($query) use ($tipo) {
                $query->where('membresia_membros.vinculo', $tipo);
            })
            ->when(request()->get('distrito_id'), function ($query) {
                $query->where('distrito.id', request()->get('distrito_id'));
            })
            ->when(request()->get('igreja_id'), function ($query) {
                $query->where('igreja.id', request()->get('igreja_id'));
            })
            ->orderBy('distrito.nome')
            ->orderBy('igreja.id')
            ->get();

        $gceus = DB::table('instituicoes_instituicoes as igreja')
            ->select(
                'distrito.nome as distrito_nome',
                'igreja.id as id_igreja',
                'igreja.nome as igreja_nome',
                'gceu.*'
            )->join('instituicoes_instituicoes as distrito', function ($join) {
                $join->on('distrito.id', '=', 'igreja.instituicao_pai_id');
            })
            ->join('gceu_cadastros as gceu', function ($join) {
                $join->on('gceu.instituicao_id', '=', 'igreja.id');
            })->join('instituicoes_instituicoes as regiao', function ($join) {
                $join->on('regiao.id', '=', 'distrito.instituicao_pai_id');
            })
            ->where(['regiao.id' => $regiaoId, 'gceu.status' => 'A'])
            ->orderBy('igreja.nome')
            ->get();

        $distritos = DB::table('instituicoes_instituicoes as distrito')
            ->select(
                'distrito.id',
                'distrito.nome as distrito_nome',
            )->join('instituicoes_instituicoes as regiao', function ($join) {
                $join->on('regiao.id', '=', 'distrito.instituicao_pai_id');
            })
            ->where(['regiao.id' => $regiaoId, 'distrito.tipo_instituicao_id' => 2])
            ->orderBy('distrito.nome')
            ->get();
        
    
        $igrejas = DB::table('instituicoes_instituicoes as igreja')
        ->select(
            'igreja.id as id_igreja',
            'igreja.nome as igreja_nome',
        )->join('instituicoes_instituicoes as distrito', function ($join) {
            $join->on('distrito.id', '=', 'igreja.instituicao_pai_id');
        })->join('instituicoes_instituicoes as regiao', function ($join) {
            $join->on('regiao.id', '=', 'distrito.instituicao_pai_id');
        })
        ->where(['regiao.id' => $regiaoId, 'igreja.tipo_instituicao_id' => 1])
        ->orderBy('igreja.nome')
        ->get();
        
        $funcoes = GCeuFuncoes::get();
        return ['dados' => $dados, 'funcoes' => $funcoes, 'gceus' => $gceus, 'igrejas' => $igrejas, 'distritos' => $distritos];
    }

    public function getFuncao($id)
    {
        return  GCeuFuncoes::find($id);
    }
    
}
