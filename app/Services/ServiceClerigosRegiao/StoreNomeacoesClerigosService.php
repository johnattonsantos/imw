<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoaNomeacao;
use App\Traits\RegionalScope;


class StoreNomeacoesClerigosService
{
    use RegionalScope;

    public function execute($request)
    {
        $data = $request->safe()->only([
            'funcao_ministerial_id',
            'data_nomeacao',
            'instituicao_id',
            'pessoa_id'

        ]);

        $regiaoId = $this->sessionRegiaoId();
        if (
            !$this->pessoaPertenceRegiao((int) ($data['pessoa_id'] ?? 0), $regiaoId) ||
            !$this->instituicaoPertenceRegiao((int) ($data['instituicao_id'] ?? 0), $regiaoId)
        ) {
            throw new \InvalidArgumentException('Não foi possível salvar a nomeação fora da região do perfil.');
        }

        PessoaNomeacao::create($data);
    }
}
