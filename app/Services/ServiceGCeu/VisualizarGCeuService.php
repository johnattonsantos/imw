<?php

namespace App\Services\ServiceGCeu;

use App\Models\GCeu;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class VisualizarGCeuService 
{
    public function findOne($id): ?GCeu
    {
        $gceu = GCeu::select('gceu_cadastros.*', 'congregacoes_congregacoes.nome as congregacao', 'instituicoes_instituicoes.nome as igreja',
            DB::raw("(SELECT membresia_membros.nome FROM gceu_membros JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id WHERE gceu_funcao_id = 7 AND gceu_membros.gceu_cadastro_id = gceu_cadastros.id limit 1) anfitriao"),
            DB::raw("(SELECT CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE telefone_whatsapp END contato FROM gceu_membros JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id JOIN membresia_contatos ON membresia_contatos.membro_id = membresia_membros.id WHERE gceu_funcao_id = 7 AND gceu_membros.gceu_cadastro_id = gceu_cadastros.id limit 1) contato"),
            DB::raw("(SELECT CASE WHEN email_preferencial IS NOT NULL AND email_preferencial <> '' THEN email_preferencial
                              ELSE email_alternativo END email FROM gceu_membros JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id JOIN membresia_contatos ON membresia_contatos.membro_id = membresia_membros.id WHERE gceu_funcao_id = 7 AND gceu_membros.gceu_cadastro_id = gceu_cadastros.id limit 1) email"))
            ->leftJoin('congregacoes_congregacoes', 'congregacoes_congregacoes.id', 'gceu_cadastros.congregacao_id')
            ->leftJoin('instituicoes_instituicoes', 'instituicoes_instituicoes.id', 'gceu_cadastros.instituicao_id')
            ->where('gceu_cadastros.id', $id)->first();
        return $gceu;
    }
}
