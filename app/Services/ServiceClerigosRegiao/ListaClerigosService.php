<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoasPessoa;
use App\Traits\RegionalScope;

class ListaClerigosService
{
    use RegionalScope;

    public function execute($searchTerm = null)
    {
        $regiao_id = $this->sessionRegiaoId();
        $clerigos = PessoasPessoa::query()->withTrashed()->where('regiao_id', $regiao_id)
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where('nome', 'like', "%{$searchTerm}%");
            })
            ->orderBy('nome', 'asc')
            ->paginate(50);
        return $clerigos;
    }

    public function totalClerigo()
    {
        $regiao_id = $this->sessionRegiaoId();
        $clerigos = PessoasPessoa::query()->withTrashed()->where('regiao_id', $regiao_id)
            ->orderBy('nome', 'asc')
            ->get();
        return $clerigos;
    }
}
