<?php

namespace App\Observers;

use App\Models\InstituicoesInstituicao;
use App\Support\CpfCnpj;

class InstituicoesObserver
{
    public function creating(InstituicoesInstituicao $instituicoesInstituicao)
    {

        if ($instituicoesInstituicao->cep) {
            $instituicoesInstituicao->cep = preg_replace('/[^\d]/', '', $instituicoesInstituicao->cep);
        }

        if ($instituicoesInstituicao->telefone) {
            $instituicoesInstituicao->telefone = preg_replace('/[^\d]/', '', $instituicoesInstituicao->telefone);
        }

        if ($instituicoesInstituicao->cnpj) {
            $instituicoesInstituicao->cnpj = CpfCnpj::normalize($instituicoesInstituicao->cnpj);
        }
    }

    public function updating(InstituicoesInstituicao $instituicoesInstituicao)
    {

        if ($instituicoesInstituicao->cep) {
            $instituicoesInstituicao->cep = preg_replace('/[^\d]/', '', $instituicoesInstituicao->cep);
        }

        if ($instituicoesInstituicao->telefone) {
            $instituicoesInstituicao->telefone = preg_replace('/[^\d]/', '', $instituicoesInstituicao->telefone);
        }

        if ($instituicoesInstituicao->cnpj) {
            $instituicoesInstituicao->cnpj = CpfCnpj::normalize($instituicoesInstituicao->cnpj);
        }
    }
}
