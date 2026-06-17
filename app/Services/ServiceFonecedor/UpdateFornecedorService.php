<?php

namespace App\Services\ServiceFonecedor;

use App\Models\FinanceiroFornecedores;
use App\Support\CpfCnpj;

class UpdateFornecedorService
{
    public function execute($data, $id)
    {
        $fornecedor = FinanceiroFornecedores::findOrFail($id);
        
        $fornecedor->update([
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
            'celular' => $data['celular']
        ]);
    }
}
