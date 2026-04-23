<?php

namespace App\Services\ServiceGCeu;

use App\Models\GCeu;
use App\Models\GCeuFuncoes;
use Illuminate\Support\Facades\DB;

class GCeuRelatorioRegiaoFuncoesService
{
    public function getList($regiaoId, $distritoId, $igrejaId, $funcaoId, $gceuId, $tipo = null)
    {
        $novoConvertidoExpr = "UPPER(COALESCE(mm.novo_convertido, '')) IN ('1','S','SIM','Y','TRUE')";

        $dadosMembresia = DB::table('instituicoes_instituicoes as igreja')
            ->select(
                'distrito.id',
                'distrito.nome as distrito_nome',
                'igreja.id as id_igreja',
                'igreja.nome as igreja_nome',
                'gceu.*',
                'mm.nome as lider',
                'mm.novo_convertido',
                'mm.created_at as data_cadastro',
                'mc.telefone_preferencial',
                'gf.funcao',
                DB::raw("CASE
                    WHEN $novoConvertidoExpr THEN 'Novo Convertido'
                    WHEN mm.vinculo = 'M' THEN 'Membro'
                    WHEN mm.vinculo = 'C' THEN 'Congregado'
                    WHEN mm.vinculo = 'V' THEN 'Visitante'
                    ELSE 'Não informado'
                END as tipo"),
                DB::raw("(SELECT membresia_membros.nome
                    FROM gceu_membros
                    JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id
                    WHERE gceu_funcao_id = 7
                        AND gceu_membros.gceu_cadastro_id = gceu.id
                        AND membresia_membros.status = 'A'
                    LIMIT 1) as anfitriao"),
                DB::raw("(SELECT CASE
                    WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                    WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                    ELSE telefone_whatsapp
                END
                    FROM gceu_membros
                    JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id
                    JOIN membresia_contatos ON membresia_contatos.membro_id = membresia_membros.id
                    WHERE gceu_funcao_id = 7
                        AND gceu_membros.gceu_cadastro_id = gceu.id
                        AND membresia_membros.status = 'A'
                    LIMIT 1) as contato")
            )->join('instituicoes_instituicoes as distrito', function ($join) {
                $join->on('distrito.id', '=', 'igreja.instituicao_pai_id');
            })->join('instituicoes_instituicoes as regiao', function ($join) {
                $join->on('regiao.id', '=', 'distrito.instituicao_pai_id');
            })
            ->join('gceu_cadastros as gceu', function ($join) {
                $join->on('gceu.instituicao_id', '=', 'igreja.id');
            })
            ->join('gceu_membros as gm', 'gm.gceu_cadastro_id', '=', 'gceu.id')
            ->join('membresia_membros as mm', 'mm.id', '=', 'gm.membro_id')
            ->join('gceu_funcoes as gf', 'gf.id', '=', 'gm.gceu_funcao_id')
            ->leftJoin('membresia_contatos as mc', 'mc.membro_id', '=', 'mm.id')
            ->where(['regiao.id' => $regiaoId, 'gceu.status' => 'A'])
            ->when($funcaoId, function ($query) use ($funcaoId) {
                $query->where('gm.gceu_funcao_id', $funcaoId);
            })
            ->when($gceuId, function ($query) use ($gceuId) {
                $query->where('gceu.id', $gceuId);
            })
            ->when(in_array($tipo, ['M', 'C'], true), function ($query) use ($tipo) {
                $query->where('mm.vinculo', $tipo);
            })
            ->when($tipo === 'V', function ($query) use ($novoConvertidoExpr) {
                $query->where('mm.vinculo', 'V')->whereRaw("NOT ($novoConvertidoExpr)");
            })
            ->when($tipo === 'N', function ($query) use ($novoConvertidoExpr) {
                $query->whereRaw($novoConvertidoExpr);
            })
            ->when($distritoId, function ($query) use ($distritoId) {
                $query->where('distrito.id', $distritoId);
            })
            ->when($igrejaId, function ($query) use ($igrejaId) {
                $query->where('igreja.id', $igrejaId);
            })
            ->get();

        $dadosReuniao = collect();
        $deveIncluirReuniao = empty($funcaoId) && (!$tipo || in_array($tipo, ['V', 'N'], true));
        if ($deveIncluirReuniao) {
            $dadosReuniao = DB::table('instituicoes_instituicoes as igreja')
                ->select(
                    'distrito.id',
                    'distrito.nome as distrito_nome',
                    'igreja.id as id_igreja',
                    'igreja.nome as igreja_nome',
                    'gceu.*',
                    'grp.nome as lider',
                    DB::raw("CASE WHEN grp.tipo = 'N' THEN 'S' ELSE 'N' END as novo_convertido"),
                    'grp.created_at as data_cadastro',
                    'grp.contato as telefone_preferencial',
                    DB::raw("'Cadastro da Reunião' as funcao"),
                    DB::raw("CASE
                        WHEN grp.tipo = 'N' THEN 'Novo Convertido'
                        ELSE 'Visitante'
                    END as tipo"),
                    DB::raw("(SELECT membresia_membros.nome
                        FROM gceu_membros
                        JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id
                        WHERE gceu_funcao_id = 7
                            AND gceu_membros.gceu_cadastro_id = gceu.id
                            AND membresia_membros.status = 'A'
                        LIMIT 1) as anfitriao"),
                    DB::raw("(SELECT CASE
                        WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                        WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                        ELSE telefone_whatsapp
                    END
                        FROM gceu_membros
                        JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id
                        JOIN membresia_contatos ON membresia_contatos.membro_id = membresia_membros.id
                        WHERE gceu_funcao_id = 7
                            AND gceu_membros.gceu_cadastro_id = gceu.id
                            AND membresia_membros.status = 'A'
                        LIMIT 1) as contato")
                )->join('instituicoes_instituicoes as distrito', function ($join) {
                    $join->on('distrito.id', '=', 'igreja.instituicao_pai_id');
                })->join('instituicoes_instituicoes as regiao', function ($join) {
                    $join->on('regiao.id', '=', 'distrito.instituicao_pai_id');
                })
                ->join('gceu_cadastros as gceu', function ($join) {
                    $join->on('gceu.instituicao_id', '=', 'igreja.id');
                })
                ->join('gceu_reuniao_pessoas as grp', 'grp.gceu_cadastro_id', '=', 'gceu.id')
                ->where(['regiao.id' => $regiaoId, 'gceu.status' => 'A'])
                ->when($distritoId, function ($query) use ($distritoId) {
                    $query->where('distrito.id', $distritoId);
                })
                ->when($igrejaId, function ($query) use ($igrejaId) {
                    $query->where('igreja.id', $igrejaId);
                })
                ->when($gceuId, function ($query) use ($gceuId) {
                    $query->where('gceu.id', $gceuId);
                })
                ->when($tipo === 'V', function ($query) {
                    $query->where('grp.tipo', 'V');
                })
                ->when($tipo === 'N', function ($query) {
                    $query->where('grp.tipo', 'N');
                })
                ->get();
        }

        $dados = $dadosMembresia
            ->concat($dadosReuniao)
            ->sortBy(function ($item) {
                return sprintf(
                    '%s|%08d|%s',
                    strtolower((string) ($item->distrito_nome ?? '')),
                    (int) ($item->id_igreja ?? 0),
                    strtolower((string) ($item->lider ?? ''))
                );
            })
            ->values();

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
