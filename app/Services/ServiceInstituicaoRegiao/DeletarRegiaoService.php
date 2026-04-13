<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use Illuminate\Validation\ValidationException;

class DeletarRegiaoService
{
    public function execute($id)
    {
        $instituicao = InstituicoesInstituicao::findOrFail($id);

        throw ValidationException::withMessages([
            'error' => "Para inativar a instituição {$instituicao->nome}, use a edição do cadastro, altere o Status para Inativo e informe a Data de Encerramento.",
        ]);
    }
}
