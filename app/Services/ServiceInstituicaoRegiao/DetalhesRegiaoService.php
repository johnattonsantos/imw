<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Traits\RegionalScope;

class DetalhesRegiaoService
{
    use RegionalScope;

    public function execute($id)
    {
        // Busca o instituicao pelo ID
        $instituicao = InstituicoesInstituicao::where('id', $id)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();
        
        return $instituicao;
    }
}
