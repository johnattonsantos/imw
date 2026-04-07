<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Traits\RegionalScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StoreRegiaoService
{
    use RegionalScope;

    public function execute($request)
    {
        $dataAbertura = Carbon::parse($request->input('data_abertura'))->format('Y-m-d');
        $cep = str_replace('.', '', $request->input('cep'));
        $regiaoId = $this->sessionRegiaoId();
        $instituicaoPaiId = (int) $request->input('instituicao_pai_id');

        if ($instituicaoPaiId > 0 && !$this->instituicaoPertenceRegiao($instituicaoPaiId, $regiaoId)) {
            throw new \InvalidArgumentException('Instituição pai fora da região do perfil.');
        }

        InstituicoesInstituicao::create(
            [
                'nome' => $request->input('nome'),
                'tipo_instituicao_id' => $request->input('tipo_instituicao_id'),
                'instituicao_pai_id' => $instituicaoPaiId,
                'regiao_id' => $regiaoId,
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
                'inss' => 0
            ]
        );
    }
}
