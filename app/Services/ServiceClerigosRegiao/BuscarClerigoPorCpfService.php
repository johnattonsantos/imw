<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Exceptions\PessoaNotFoundException;
use App\Models\PessoasPessoa;
use App\Traits\RegionalScope;

class BuscarClerigoPorCpfService
{
    use RegionalScope;

    public function execute(string $cpf): PessoasPessoa|null
    {
        return PessoasPessoa::where('cpf', $cpf)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOr(fn () => throw new PessoaNotFoundException('Nenhuma pessoa encontrada com o CPF informado'));
    }
}
