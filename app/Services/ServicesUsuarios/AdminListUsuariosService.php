<?php

namespace App\Services\ServicesUsuarios;

use App\Models\InstituicoesInstituicao;
use App\Models\Perfil;
use App\Models\User;

class AdminListUsuariosService
{
    public function execute($parameters = null, $local)
    {
        return [
            'usuarios' => $this->handleListaMembros($parameters, $local),
        ];
    }

    private function handleListaMembros($parameters = null, $local)
    {
        $isCrie = $this->isCrieProfile();
        $isRegionalAdmin = $this->isRegionalAdminProfile();
        $hasRegionalScope = $isCrie || $isRegionalAdmin;
        $regiaoId = $hasRegionalScope ? $this->resolveCurrentRegionId() : 0;

        $query = User::query()
          ->when(isset($parameters['search']), function ($query) use ($parameters) {
            $searchTerm = $parameters['search'];
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%$searchTerm%")
                      ->orWhere('email', 'like', "%$searchTerm%");
            });
        })
        ->when($local === "L", function ($query) {
            $query->whereHas('perfilUser.instituicao', function ($subquery) {
                $subquery->where('id', session()->get('session_perfil')->instituicao_id);
            });
        });

        if ($hasRegionalScope) {
            $query->where(function ($subquery) use ($regiaoId) {
                $subquery->where('users.regiao_id', $regiaoId)
                    ->orWhereHas('perfilUser.instituicao', function ($q) use ($regiaoId) {
                        $q->where('instituicoes_instituicoes.id', $regiaoId)
                            ->orWhere('instituicoes_instituicoes.regiao_id', $regiaoId);
                    });
            });
        }

        $query->with([
            'perfilUser' => function ($subquery) use ($hasRegionalScope, $regiaoId) {
                if (!$hasRegionalScope) {
                    return;
                }

                $subquery->whereHas('instituicao', function ($q) use ($regiaoId) {
                    $q->where('instituicoes_instituicoes.id', $regiaoId)
                        ->orWhere('instituicoes_instituicoes.regiao_id', $regiaoId);
                });
            },
            'perfilUser.perfil',
            'perfilUser.instituicao',
        ]);

        return $query->paginate(100);
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
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE)
            && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
    }

    private function resolveCurrentRegionId(): int
    {
        $instituicaoId = (int) optional(session('session_perfil'))->instituicao_id;
        if ($instituicaoId <= 0) {
            return 0;
        }

        $regiaoId = (int) InstituicoesInstituicao::where('id', $instituicaoId)->value('regiao_id');
        return $regiaoId > 0 ? $regiaoId : $instituicaoId;
    }
}
