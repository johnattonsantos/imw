<?php

namespace App\Services\ServicesUsuarios;

use App\Models\Perfil;

class NovoUsuarioService
{

    public function execute()
    {
        // Obtém o perfil_id da sessão
        $perfilID = session('session_perfil')->perfil_id;
        
        // Recupera o nível do perfil baseado no perfil_id
        $perfilUsuario = Perfil::where('id', $perfilID)->first();
        
        // Verifica o nível do perfil
        if ($perfilUsuario) {
            $nivelPerfil = $perfilUsuario->nivel;
            $perfis = Perfil::orderBy('nome', 'asc')->get();

            if (Perfil::correspondeCodigo($perfilUsuario->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA)) {
                // No módulo local por instituição, administrador_sistema não deve ser vinculado.
                return $perfis->filter(function ($perfil) {
                    return Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE);
                })->values();
            }

            $perfisNivel = $perfis->where('nivel', $nivelPerfil)->values();
            if (Perfil::correspondeCodigo($perfilUsuario->nome, Perfil::CODIGO_CRIE)) {
                $perfisNivel = $perfisNivel->reject(function ($perfil) {
                    return Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE);
                })->values();
            }
            if ($this->isRegionalAdminProfile($perfilUsuario)) {
                return $perfis->reject(function ($perfil) {
                    return Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE)
                        || Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA)
                        || $this->isRegionalAdminTargetProfile($perfil);
                })->values();
            }

            return $perfisNivel;
        }

        return null; // Retorna null se não encontrar o perfil
    }

    private function isRegionalAdminProfile(Perfil $perfil): bool
    {
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
