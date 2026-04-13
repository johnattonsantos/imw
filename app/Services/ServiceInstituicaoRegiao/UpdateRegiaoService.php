<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class UpdateRegiaoService
{
    public function execute($request, $id)
    {
        $instituicao = InstituicoesInstituicao::findOrFail($id);
        $dataAbertura = Carbon::parse($request->input('data_abertura'))->format('Y-m-d');
        $ativo = (int) $request->boolean('ativo');

        if ((int) $instituicao->ativo === 1 && $ativo === 0) {
            app(ValidaInativacaoInstituicaoService::class)->execute($instituicao);
        }

        $cep = str_replace('.', '', $request->input('cep'));
        $payload = [
            'nome' => $request->input('nome'),
            'tipo_instituicao_id' => $request->input('tipo_instituicao_id'),
            'instituicao_pai_id' => $request->input('instituicao_pai_id'),
            'regiao_id' => $request->input('regiao_id'),
            'bairro' => $request->input('bairro'),
            'cep' => $cep,
            'cidade' => $request->input('cidade'),
            'cnpj' => $request->input('cnpj'),
            'complemento' => $request->input('complemento'),
            'data_abertura' => $dataAbertura,
            'numero' => $request->input('numero'),
            'pais' => $request->input('pais'),
            'uf' => $request->input('uf'),
            'endereco' => $request->input('endereco'),
            'telefone' => $request->input('telefone'),
            'ddd' => $request->input('ddd'),
            'ativo' => $ativo,
            'inss' => 0,
        ];

        if (Schema::hasColumn('instituicoes_instituicoes', 'data_encerramento')) {
            $dataEncerramento = $request->filled('data_encerramento')
                ? Carbon::parse($request->input('data_encerramento'))->format('Y-m-d')
                : null;
            $payload['data_encerramento'] = $ativo === 0 ? $dataEncerramento : null;
        }

        $instituicao->update($payload);

    }
}
