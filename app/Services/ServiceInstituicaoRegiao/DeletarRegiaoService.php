<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Traits\RegionalScope;

class DeletarRegiaoService
{
    use RegionalScope;

    public function execute($id)
    {
        $instituicao = InstituicoesInstituicao::where('id', $id)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();
        $instituicao->delete(); // Soft delete
    }
}
