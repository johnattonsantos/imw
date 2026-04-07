<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Traits\RegionalScope;

class ListarRegiaoServices
{
    use RegionalScope;

    public function execute($parameters = null, $tipoInstituicaoId)
    {
        $regiaoId = $this->sessionRegiaoId();

        return InstituicoesInstituicao::withTrashed()
            ->when(isset($parameters['search']) && !empty($parameters['search']), function ($query) use ($parameters) {
                $searchTerm = $parameters['search'];
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('nome', 'like', "%$searchTerm%")
                        ->orWhere('email', 'like', "%$searchTerm%")
                        ->orWhere('cidade', 'like', "%$searchTerm%")
                        ->orWhere('telefone', 'like', "%$searchTerm%");
                });
            })
            ->where('regiao_id', $regiaoId)
            ->when($tipoInstituicaoId, function ($query) use ($tipoInstituicaoId) {
                $query->where('tipo_instituicao_id', $tipoInstituicaoId);
            })
            ->orderBy('nome', 'asc')
            ->paginate(50);
    }
}
