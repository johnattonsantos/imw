<?php

namespace App\Services\ServiceGCeu;

use App\Models\GCeu;
use App\Models\GCeuFuncoes;
use Illuminate\Support\Facades\DB;

class GCeuRelatorioFuncoesService
{
    public function getList($igrejaId, $funcaoId, $gceuId, $tipo = null)
    {
        $novoConvertidoExpr = "UPPER(COALESCE(mm.novo_convertido, '')) IN ('1','S','SIM','Y','TRUE')";

        $dadosMembresia = DB::table('gceu_cadastros as gc')
            ->select(
                'gc.*',
                'gf.funcao',
                'mm.nome as lider',
                'mm.novo_convertido',
                'mm.created_at as data_cadastro',
                'mc.telefone_preferencial',
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
                        AND gceu_membros.gceu_cadastro_id = gc.id
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
                        AND gceu_membros.gceu_cadastro_id = gc.id
                        AND membresia_membros.status = 'A'
                    LIMIT 1) as contato")
            )
            ->join('gceu_membros as gm', 'gm.gceu_cadastro_id', '=', 'gc.id')
            ->join('membresia_membros as mm', 'mm.id', '=', 'gm.membro_id')
            ->join('gceu_funcoes as gf', 'gf.id', '=', 'gm.gceu_funcao_id')
            ->leftJoin('membresia_contatos as mc', 'mc.membro_id', '=', 'mm.id')
            ->where(['gc.instituicao_id' => $igrejaId, 'gc.status' => 'A'])
            ->when($funcaoId, function ($query) use ($funcaoId) {
                $query->where('gm.gceu_funcao_id', $funcaoId);
            })
            ->when($gceuId, function ($query) use ($gceuId) {
                $query->where('gc.id', $gceuId);
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
            ->get();

        $dadosReuniao = collect();
        $deveIncluirReuniao = empty($funcaoId) && (!$tipo || in_array($tipo, ['V', 'N'], true));
        if ($deveIncluirReuniao) {
            $dadosReuniao = DB::table('gceu_reuniao_pessoas as grp')
                ->join('gceu_cadastros as gc', 'gc.id', '=', 'grp.gceu_cadastro_id')
                ->select(
                    'gc.*',
                    DB::raw("'Cadastro da Reunião' as funcao"),
                    'grp.nome as lider',
                    DB::raw("CASE WHEN grp.tipo = 'N' THEN 'S' ELSE 'N' END as novo_convertido"),
                    'grp.created_at as data_cadastro',
                    'grp.contato as telefone_preferencial',
                    DB::raw("CASE
                        WHEN grp.tipo = 'N' THEN 'Novo Convertido'
                        ELSE 'Visitante'
                    END as tipo"),
                    DB::raw("(SELECT membresia_membros.nome
                        FROM gceu_membros
                        JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id
                        WHERE gceu_funcao_id = 7
                            AND gceu_membros.gceu_cadastro_id = gc.id
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
                            AND gceu_membros.gceu_cadastro_id = gc.id
                            AND membresia_membros.status = 'A'
                        LIMIT 1) as contato")
                )
                ->where('grp.instituicao_id', $igrejaId)
                ->where('gc.instituicao_id', $igrejaId)
                ->where('gc.status', 'A')
                ->when($gceuId, function ($query) use ($gceuId) {
                    $query->where('grp.gceu_cadastro_id', $gceuId);
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
            ->sortByDesc(function ($item) {
                return $item->data_cadastro ?? null;
            })
            ->values();

        $funcoes = GCeuFuncoes::get();
        $gceus = GCeu::where(['instituicao_id' => $igrejaId])->get();
        return ['dados' => $dados, 'funcoes' => $funcoes, 'gceus' => $gceus];
    }

    public function getFuncao($id)
    {
        return  GCeuFuncoes::find($id);
    }
    
}
