<?php

namespace App\Services\ServiceFonecedor;

use App\Models\FinanceiroFornecedores;
use App\Models\PerfilUser;
use App\Support\CpfCnpj;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SalvarFornecedorService
{

    public function execute($data)
    {
        FinanceiroFornecedores::create([
            'cpfcnpj' => CpfCnpj::normalize($data['cpf_cnpj']),
            'nome' => $data['nome'],
            'email' => $data['email'],
            'site' => $data['site'],
            'cep' => $data['cep'],
            'logradouro' => $data['endereco'],
            'numero' => $data['numero'],
            'complemento' => $data['complemento'],
            'bairro' => $data['bairro'],
            'cidade' => $data['cidade'],
            'uf' => $data['uf'],
            'pais' => $data['pais'],
            'telefone' => $data['telefone'],
            'celular' => $data['celular'],
            'instituicao_id' => session()->get('session_perfil')->instituicao_id,
        ]);
    }
}
