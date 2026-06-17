<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class UpdateRegiaoService
{
    public function execute($request, $id)
    {
        $regiaoId = $this->regiaoLogadaId();
        $instituicao = InstituicoesInstituicao::where('regiao_id', $regiaoId)->findOrFail($id);
        $tipoInstituicaoId = (int) $request->input('tipo_instituicao_id');
        $instituicaoPaiId = (int) $request->input('instituicao_pai_id');

        $this->assertInstituicaoPaiPertenceRegiao($tipoInstituicaoId, $instituicaoPaiId, $regiaoId);

        $dataAbertura = Carbon::parse($request->input('data_abertura'))->format('Y-m-d');
        $ativo = (int) $request->boolean('ativo');

        if ((int) $instituicao->ativo === 1 && $ativo === 0) {
            app(ValidaInativacaoInstituicaoService::class)->execute($instituicao);
        }

        $cep = str_replace('.', '', $request->input('cep'));
        $payload = [
            'nome' => $request->input('nome'),
            'tipo_instituicao_id' => $tipoInstituicaoId,
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

    private function regiaoLogadaId(): int
    {
        return (int) session()->get('session_perfil')->instituicao_id;
    }

    private function assertInstituicaoPaiPertenceRegiao(int $tipoInstituicaoId, int $instituicaoPaiId, int $regiaoId): void
    {
        $query = InstituicoesInstituicao::where('id', $instituicaoPaiId);

        if ($tipoInstituicaoId === InstituicoesTipoInstituicao::IGREJA_LOCAL) {
            $query->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
                ->where('regiao_id', $regiaoId);
        } else {
            $query->where('tipo_instituicao_id', InstituicoesTipoInstituicao::REGIAO)
                ->where('id', $regiaoId);
        }

        if (!$query->exists()) {
            throw ValidationException::withMessages([
                'instituicao_pai_id' => 'A instituição pai selecionada não pertence à região logada.',
            ]);
        }
    }
}
