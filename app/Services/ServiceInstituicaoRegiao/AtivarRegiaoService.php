<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Traits\RegionalScope;

class AtivarRegiaoService
{
    use RegionalScope;

    public function execute($id)
    {
        $instituicao = InstituicoesInstituicao::withTrashed()
            ->where('id', $id)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();
        $instituicao->restore(); // Restaurar o soft delete
    }
}
