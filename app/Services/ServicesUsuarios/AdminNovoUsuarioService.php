<?php

namespace App\Services\ServicesUsuarios;

use App\Models\Perfil;

class AdminNovoUsuarioService
{

    public function execute()
    {
        $perfils = Perfil::query()
            ->orderBy('nome', 'asc')
            ->get();

        if ($this->isCrieProfile()) {
            $perfils = $perfils->reject(function ($perfil) {
                return Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA)
                    || Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE);
            })->values();
        }

        if ($this->isRegionalAdminProfile()) {
            $perfils = $perfils->reject(function ($perfil) {
                return Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE)
                    || Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA)
                    || $this->isRegionalAdminTargetProfile($perfil);
            })->values();
        }

        return $perfils;
    }

    private function isCrieProfile(): bool
    {
        $perfilNome = (string) optional(session('session_perfil'))->perfil_nome;
        return Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_CRIE);
    }

    private function isRegionalAdminProfile(): bool
    {
        $perfilId = (int) optional(session('session_perfil'))->perfil_id;
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
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE);
    }

    private function isRegionalAdminTargetProfile(Perfil $perfil): bool
    {
        $nomeNormalizado = Perfil::normalizarNome($perfil->nome);
        return $perfil->nivel === Perfil::NIVEL_REGIAO
            && str_contains($nomeNormalizado, 'administrador')
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE)
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
    }
}
