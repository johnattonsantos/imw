<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use Illuminate\Support\Facades\Schema;

class AtivarRegiaoService
{
    public function execute($id)
    {
        $instituicao = InstituicoesInstituicao::withTrashed()->findOrFail($id);
        $payload = ['ativo' => 1];
        if (Schema::hasColumn('instituicoes_instituicoes', 'data_encerramento')) {
            $payload['data_encerramento'] = null;
        }
        $instituicao->update($payload);
        $instituicao->restore(); // Restaurar o soft delete
    }
}
