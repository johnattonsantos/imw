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
                return Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
            })->values();
        }

        return $perfils;
    }

    private function isCrieProfile(): bool
    {
        $perfilNome = (string) optional(session('session_perfil'))->perfil_nome;
        return Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_CRIE);
    }
}
