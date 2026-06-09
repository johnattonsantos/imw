<?php

namespace App\Services\ServiceNomeacoes;

use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\PessoaNomeacao;
use Illuminate\Validation\ValidationException;


class StoreNomeacoesClerigos
{
    public function execute($request)
    {
        $instituicaoId = (int) $request['instituicao_id'];
        $this->assertInstituicaoPertenceRegiaoLogada($instituicaoId);

        PessoaNomeacao::create([
            'data_nomeacao' => $request['data_nomeacao'],
            'instituicao_id' => $instituicaoId,
            'pessoa_id' => $request['pessoa_id'],
            'funcao_ministerial_id' => $request['funcao_ministerial_id'],
        ]);
    }

    private function assertInstituicaoPertenceRegiaoLogada(int $instituicaoId): void
    {
        $regiaoId = (int) session()->get('session_perfil')->instituicao_id;

        $pertenceRegiao = InstituicoesInstituicao::where('id', $instituicaoId)
            ->where(function ($query) use ($regiaoId) {
                $query->where('id', $regiaoId)
                    ->orWhere('regiao_id', $regiaoId)
                    ->orWhereIn('instituicao_pai_id', function ($subQuery) use ($regiaoId) {
                        $subQuery->select('id')
                            ->from('instituicoes_instituicoes')
                            ->where('regiao_id', $regiaoId)
                            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO);
                    });
            })
            ->exists();

        if (!$pertenceRegiao) {
            throw ValidationException::withMessages([
                'instituicao_id' => 'A instituição selecionada não pertence à região logada.',
            ]);
        }
    }
}
