<?php

namespace App\Services\ServiceGCeu;

use App\Models\GCeu;
use Illuminate\Support\Facades\DB;

class GCeuRelatorioGceuService
{
    public function getList($igrejaId)
    {
        $dados =  GCeu::select('gceu_cadastros.*',
        DB::raw("(SELECT membresia_membros.nome FROM gceu_membros JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id WHERE gceu_funcao_id = 7 AND gceu_membros.gceu_cadastro_id = gceu_cadastros.id AND membresia_membros.status = 'A' limit 1) anfitriao"),
            DB::raw("(SELECT CASE WHEN telefone_preferencial IS NOT NULL AND telefone_preferencial <> '' THEN telefone_preferencial
                              WHEN telefone_alternativo IS NOT NULL AND telefone_alternativo <> '' THEN telefone_alternativo
                              ELSE telefone_whatsapp END contato FROM gceu_membros JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id JOIN membresia_contatos ON membresia_contatos.membro_id = membresia_membros.id WHERE gceu_funcao_id = 7 AND gceu_membros.gceu_cadastro_id = gceu_cadastros.id AND membresia_membros.status = 'A' limit 1) contato"),
            DB::raw("(SELECT CASE WHEN email_preferencial IS NOT NULL AND email_preferencial <> '' THEN email_preferencial
                              ELSE email_alternativo END email FROM gceu_membros JOIN membresia_membros ON membresia_membros.id = gceu_membros.membro_id JOIN membresia_contatos ON membresia_contatos.membro_id = membresia_membros.id WHERE gceu_funcao_id = 7 AND gceu_membros.gceu_cadastro_id = gceu_cadastros.id AND membresia_membros.status = 'A' limit 1) email")
        )->where(['gceu_cadastros.instituicao_id' => $igrejaId, 'gceu_cadastros.status' => 'A'])->get();
        return ['dados' => $dados];
    }
}