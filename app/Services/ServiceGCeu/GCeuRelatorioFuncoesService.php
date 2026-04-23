<?php

namespace App\Services\ServiceGCeu;

use App\Models\GCeu;
use App\Models\GCeuFuncoes;
use Illuminate\Support\Facades\DB;

class GCeuRelatorioFuncoesService
{
    public function getList($igrejaId, $funcaoId, $gceuId, $tipo = null)
    {
        $dados =  GCeu::select(
        'gceu_cadastros.*',
        'gceu_funcoes.funcao',
        'membresia_membros.nome as lider',
        'membresia_membros.novo_convertido',
        'membresia_membros.created_at as data_cadastro',
        'membresia_contatos.telefone_preferencial',
        DB::raw("CASE membresia_membros.vinculo
                    WHEN 'M' THEN 'Membro'
                    WHEN 'C' THEN 'Congregado'
                    WHEN 'V' THEN 'Visitante'
                    ELSE 'Não informado'
                 END as tipo"),
        DB::raw("(SELECT membresia_membros.nome FROM gceu_membros JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id WHERE gceu_funcao_id = 7 AND gceu_membros.gceu_cadastro_id = gceu_cadastros.id AND membresia_membros.status = 'A' limit 1) anfitriao"),
            DB::raw("(SELECT CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE telefone_whatsapp END contato FROM gceu_membros JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id JOIN membresia_contatos ON membresia_contatos.membro_id = membresia_membros.id WHERE gceu_funcao_id = 7 AND gceu_membros.gceu_cadastro_id = gceu_cadastros.id  AND membresia_membros.status = 'A' limit 1) contato")
        )
                ->join('gceu_membros', 'gceu_membros.gceu_cadastro_id', 'gceu_cadastros.id')
                ->join('membresia_membros', 'membresia_membros.id', 'gceu_membros.membro_id')
                ->join('gceu_funcoes', 'gceu_funcoes.id', 'gceu_membros.gceu_funcao_id')
                ->leftJoin('membresia_contatos', 'membresia_contatos.membro_id', 'membresia_membros.id')
                ->where(['gceu_cadastros.instituicao_id' => $igrejaId, 'gceu_cadastros.status' => 'A'])
                ->when($funcaoId, function ($query) use ($funcaoId) {
                    $query->where('gceu_membros.gceu_funcao_id', $funcaoId);
                })
                ->when($gceuId, function ($query) use ($gceuId) {
                    $query->where('gceu_cadastros.id', $gceuId);
                })
                ->when($tipo, function ($query) use ($tipo) {
                    $query->where('membresia_membros.vinculo', $tipo);
                })
                ->get();
        $funcoes = GCeuFuncoes::get();
        $gceus = GCeu::where(['instituicao_id' => $igrejaId])->get();
        return ['dados' => $dados, 'funcoes' => $funcoes, 'gceus' => $gceus];
    }

    public function getFuncao($id)
    {
        return  GCeuFuncoes::find($id);
    }
    
}
