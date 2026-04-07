<?php

namespace App\Services\ServiceClerigosRegiao;


use App\Models\PessoasPessoa;
use App\Traits\RegionalScope;

class AtivarClerigoService
{
    use RegionalScope;

    public function execute($id)
    {
        $clerigo = PessoasPessoa::withTrashed()
            ->where('id', $id)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();
        $clerigo->restore(); // Restaurar o soft delete
    }
}
