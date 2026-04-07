<?php

namespace App\Services\ServiceNomeacoes;

use App\Models\PessoaNomeacao;
use App\Traits\RegionalScope;


class StoreNomeacoesClerigos
{
    use RegionalScope;

    public function execute($request)
    {
        $regiaoId = $this->sessionRegiaoId();
        $pessoaId = (int) $request['pessoa_id'];
        $instituicaoId = (int) $request['instituicao_id'];

        if (!$this->pessoaPertenceRegiao($pessoaId, $regiaoId) || !$this->instituicaoPertenceRegiao($instituicaoId, $regiaoId)) {
            throw new \InvalidArgumentException('Não foi possível salvar a nomeação fora da região do perfil.');
        }

        PessoaNomeacao::create([
            'data_nomeacao' => $request['data_nomeacao'],
            'instituicao_id' => $instituicaoId,
            'pessoa_id' => $pessoaId,
            'funcao_ministerial_id' => $request['funcao_ministerial_id'],
        ]);
    }
}
