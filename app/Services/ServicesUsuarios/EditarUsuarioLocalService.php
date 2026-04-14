<?php

namespace App\Services\ServicesUsuarios;

use App\Models\InstituicoesInstituicao;
use App\Models\Perfil;
use App\Models\PerfilUser;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class EditarUsuarioLocalService
{
    public function execute($data, $id)
    {
        $user = User::findOrFail($id);
        $perfilID = (int) ($data['perfil_id'] ?? 0);
        $instituicaoSessaoId = (int) session()->get('session_perfil')->instituicao_id;
        $isAdminSistema = $this->isAdministradorSistemaPerfil($perfilID);
        $isCrie = $this->isCriePerfil($perfilID);
        $isRegionalAdminTarget = $this->isRegionalAdminPerfil($perfilID);
        $isCurrentCrie = $this->isCurrentProfileCrie();
        $isCurrentRegionalAdmin = $this->isCurrentRegionalAdminProfile();
        $regiaoId = $this->resolveRegiaoId($instituicaoSessaoId);
        $instituicaoVinculoId = $isCrie ? $regiaoId : $instituicaoSessaoId;

        if ($isAdminSistema) {
            throw new \Exception('O perfil administrador_sistema não pode ser vinculado por instituição. Utilize o módulo Admin.');
        }

        if ($isCurrentCrie && $isCrie) {
            throw new \Exception('Perfil CRIE não pode criar ou atribuir perfil CRIE.');
        }
        if ($isCurrentRegionalAdmin && $isCrie) {
            throw new \Exception('Administrador da Região não pode criar ou atribuir perfil CRIE.');
        }
        if ($isCurrentRegionalAdmin && $isRegionalAdminTarget) {
            throw new \Exception('Administrador da Região não pode criar ou atribuir perfil ADMINISTRADOR DA REGIÃO.');
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf'  => preg_replace('/[^0-9]/', '', $data['cpf']),
            'telefone' => preg_replace('/[^0-9]/', '', $data['telefone']),
            'pessoa_id' => $data['pessoa_id'] ?? null,
            'regiao_id' => $isCrie ? $regiaoId : $user->regiao_id,
        ]);

        if (isset($data['password']) && !empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        PerfilUser::where('user_id', $user->id)
            ->whereIn('instituicao_id', array_values(array_unique([$instituicaoSessaoId, $regiaoId])))
            ->delete();

        PerfilUser::create([
                'user_id' => $user->id,
                'perfil_id' => $perfilID,
                'instituicao_id' => $instituicaoVinculoId,
        ]);

    }

    private function isCriePerfil(int $perfilId): bool
    {
        if ($perfilId <= 0) {
            return false;
        }

        $perfilNome = (string) Perfil::where('id', $perfilId)->value('nome');
        return Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_CRIE);
    }

    private function isAdministradorSistemaPerfil(int $perfilId): bool
    {
        if ($perfilId <= 0) {
            return false;
        }

        $perfilNome = (string) Perfil::where('id', $perfilId)->value('nome');
        return Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
    }

    private function resolveRegiaoId(int $instituicaoId): int
    {
        if ($instituicaoId <= 0) {
            return 0;
        }

        $regiaoId = (int) InstituicoesInstituicao::where('id', $instituicaoId)->value('regiao_id');
        return $regiaoId > 0 ? $regiaoId : $instituicaoId;
    }

    private function isRegionalAdminPerfil(int $perfilId): bool
    {
        if ($perfilId <= 0) {
            return false;
        }

        $perfil = Perfil::find($perfilId);
        if (!$perfil) {
            return false;
        }

        $nomeNormalizado = Perfil::normalizarNome($perfil->nome);
        return $perfil->nivel === Perfil::NIVEL_REGIAO
            && str_contains($nomeNormalizado, 'administrador')
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE)
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
    }

    private function isCurrentProfileCrie(): bool
    {
        $perfilNomeSessao = (string) optional(session('session_perfil'))->perfil_nome;
        return Perfil::correspondeCodigo($perfilNomeSessao, Perfil::CODIGO_CRIE);
    }

    private function isCurrentRegionalAdminProfile(): bool
    {
        $perfilIdSessao = (int) optional(session('session_perfil'))->perfil_id;
        if ($perfilIdSessao <= 0) {
            return false;
        }

        $perfilSessao = Perfil::find($perfilIdSessao);
        if (!$perfilSessao) {
            return false;
        }

        $nomeNormalizado = Perfil::normalizarNome($perfilSessao->nome);
        return $perfilSessao->nivel === Perfil::NIVEL_REGIAO
            && str_contains($nomeNormalizado, 'administrador')
            && !Perfil::correspondeCodigo($perfilSessao->nome, Perfil::CODIGO_CRIE);
    }
}
