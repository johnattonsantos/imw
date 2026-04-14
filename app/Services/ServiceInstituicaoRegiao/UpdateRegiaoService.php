<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Traits\RegionalScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class UpdateRegiaoService
{
    use RegionalScope;

    public function execute($request, $id)
    {
        $regiaoId = $this->sessionRegiaoId();
        $instituicao = InstituicoesInstituicao::where('id', $id)
            ->where('regiao_id', $regiaoId)
            ->firstOrFail();
        $dataAbertura = Carbon::parse($request->input('data_abertura'))->format('Y-m-d');
        $instituicaoPaiId = (int) $request->input('instituicao_pai_id');
        $ativo = (int) $request->boolean('ativo');

        if ($instituicaoPaiId > 0 && !$this->instituicaoPertenceRegiao($instituicaoPaiId, $regiaoId)) {
            throw new \InvalidArgumentException('Instituição pai fora da região do perfil.');
        }


        if ((int) $instituicao->ativo === 1 && $ativo === 0) {
            app(ValidaInativacaoInstituicaoService::class)->execute($instituicao);
        }

        $cep = str_replace('.', '', $request->input('cep'));
        $payload = [
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
