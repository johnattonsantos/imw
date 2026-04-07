<?php

namespace App\Traits;

use App\Models\InstituicoesInstituicao;
use App\Models\Perfil;
use App\Models\PessoasPessoa;

trait RegionalScope
{
    protected function sessionRegiaoId(): int
    {
        $regiaoId = (int) data_get(session('session_perfil'), 'instituicoes.regiao.id');
        if ($regiaoId > 0) {
            return $regiaoId;
        }

        return (int) data_get(session('session_perfil'), 'instituicao_id');
    }

    protected function isCrieProfile(): bool
    {
        $perfilNome = (string) optional(session('session_perfil'))->perfil_nome;
        return Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_CRIE);
    }

    protected function pessoaPertenceRegiao(int $pessoaId, ?int $regiaoId = null): bool
    {
        $regiaoId = $regiaoId ?: $this->sessionRegiaoId();
        if ($regiaoId <= 0 || $pessoaId <= 0) {
            return false;
        }

        return PessoasPessoa::where('id', $pessoaId)
            ->where('regiao_id', $regiaoId)
            ->exists();
    }

    protected function instituicaoPertenceRegiao(int $instituicaoId, ?int $regiaoId = null): bool
    {
        $regiaoId = $regiaoId ?: $this->sessionRegiaoId();
        if ($regiaoId <= 0 || $instituicaoId <= 0) {
            return false;
        }

        $instituicao = InstituicoesInstituicao::select('id', 'regiao_id', 'instituicao_pai_id')->find($instituicaoId);
        if (!$instituicao) {
            return false;
        }

        if ((int) $instituicao->id === $regiaoId) {
            return true;
        }

        if ((int) $instituicao->regiao_id === $regiaoId) {
            return true;
        }

        if ((int) $instituicao->instituicao_pai_id === $regiaoId) {
            return true;
        }

        if ((int) $instituicao->instituicao_pai_id > 0) {
            return InstituicoesInstituicao::where('id', $instituicao->instituicao_pai_id)
                ->where('instituicao_pai_id', $regiaoId)
                ->exists();
        }

        return false;
    }
}

