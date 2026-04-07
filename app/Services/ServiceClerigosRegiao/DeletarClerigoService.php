<?php
namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoasPessoa;
use App\Traits\RegionalScope;

class DeletarClerigoService
{
    use RegionalScope;

    public function execute($id)
    {
        $clerigo = PessoasPessoa::where('id', $id)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();
        if($clerigo){
            $clerigo->delete(); // Soft delete
        }

    }
}
