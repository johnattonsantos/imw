<?php

namespace App\Services\ServicesUsuarios;

use App\Models\InstituicoesInstituicao;
use App\Models\Perfil;
use App\Models\PerfilUser;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SalvarUsuarioService
{

    public function execute($data)
    {
        $instituicoes = array_values(array_filter($data['instituicao_id'] ?? [], fn ($id) => !empty($id)));
        $perfis = array_values(array_filter($data['perfil_id'] ?? [], fn ($id) => !empty($id)));
        $temPerfilNaoGlobal = collect($perfis)->contains(function ($perfilId) {
            $nomePerfil = (string) Perfil::where('id', (int) $perfilId)->value('nome');
            return !Perfil::correspondeCodigo($nomePerfil, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);
        });

        if ($temPerfilNaoGlobal && empty($instituicoes)) {
            throw new \Exception('É necessário selecionar ao menos uma instituição.');
        }

        $regioesSelecionadas = $this->resolveRegionIdsByInstitutions($instituicoes);
        $isCrie = $this->isCrieProfile();
        $isRegionalAdmin = $this->isRegionalAdminProfile();
        $regiaoSessao = $this->resolveCurrentRegionId();

        if ($isCrie) {
            if ($this->containsPerfilCodigo($perfis, Perfil::CODIGO_CRIE)) {
                throw new \Exception('Perfil CRIE não pode criar ou atribuir perfil CRIE.');
            }
            if ($regiaoSessao <= 0) {
                throw new \Exception('Região da sessão não identificada.');
            }
            foreach ($regioesSelecionadas as $regiaoId) {
                if ($regiaoId !== $regiaoSessao) {
                    throw new \Exception('Perfil CRIE só pode gerenciar instituições da própria região.');
                }
            }
        }

        if ($isRegionalAdmin && $this->containsPerfilCodigo($perfis, Perfil::CODIGO_CRIE)) {
            throw new \Exception('Administrador da Região não pode criar ou atribuir perfil CRIE.');
        }
        if ($isRegionalAdmin && $this->containsPerfilCodigo($perfis, Perfil::CODIGO_ADMINISTRADOR_SISTEMA)) {
            throw new \Exception('Administrador da Região não pode criar ou atribuir perfil ADMINISTRADOR DO SISTEMA.');
        }
        if ($isRegionalAdmin && $this->containsRegionalAdminPerfil($perfis)) {
            throw new \Exception('Administrador da Região não pode criar ou atribuir perfil ADMINISTRADOR DA REGIÃO.');
        }
        if ($isRegionalAdmin) {
            if ($regiaoSessao <= 0) {
                throw new \Exception('Região da sessão não identificada.');
            }
            foreach ($regioesSelecionadas as $regiaoId) {
                if ($regiaoId !== $regiaoSessao) {
                    throw new \Exception('Administrador da Região só pode gerenciar instituições da própria região.');
                }
            }
        }

        $regiaoUsuario = ($isCrie || $isRegionalAdmin)
            ? $regiaoSessao
            : (count($regioesSelecionadas) === 1 ? (int) $regioesSelecionadas[0] : null);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'cpf'  => preg_replace('/[^0-9]/', '', $data['cpf']),
            'telefone' => preg_replace('/[^0-9]/', '', $data['telefone']),
            'pessoa_id' => $data['pessoa_id'] ?? null,
            'regiao_id' => $regiaoUsuario,
        ]);

        foreach ($data['perfil_id'] as $key => $perfilId) {
            $nomePerfil = (string) Perfil::where('id', (int) $perfilId)->value('nome');
            $isAdminSistemaPerfil = Perfil::correspondeCodigo($nomePerfil, Perfil::CODIGO_ADMINISTRADOR_SISTEMA);

            PerfilUser::create([
                'user_id' => $user->id,
                'perfil_id' => $perfilId,
                'instituicao_id' => $isAdminSistemaPerfil ? null : ($data['instituicao_id'][$key] ?? null),
            ]);
        }
    }

    private function isCrieProfile(): bool
    {
        $perfilNome = (string) optional(session('session_perfil'))->perfil_nome;
        return Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_CRIE);
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

    private function resolveRegionIdsByInstitutions(array $instituicoes): array
    {
        $regioes = [];
        foreach ($instituicoes as $instituicaoId) {
            $instituicao = InstituicoesInstituicao::select('id', 'regiao_id')->find((int) $instituicaoId);
            if (!$instituicao) {
                continue;
            }
            $regiaoId = (int) ($instituicao->regiao_id ?: $instituicao->id);
            if ($regiaoId > 0) {
                $regioes[] = $regiaoId;
            }
        }

        return array_values(array_unique($regioes));
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

    private function containsPerfilCodigo(array $perfilIds, string $codigo): bool
    {
        foreach ($perfilIds as $perfilId) {
            $nomePerfil = (string) Perfil::where('id', (int) $perfilId)->value('nome');
            if (Perfil::correspondeCodigo($nomePerfil, $codigo)) {
                return true;
            }
        }

        return false;
    }

    private function containsRegionalAdminPerfil(array $perfilIds): bool
    {
        foreach ($perfilIds as $perfilId) {
            $perfil = Perfil::find((int) $perfilId);
            if (!$perfil) {
                continue;
            }

            $nomeNormalizado = Perfil::normalizarNome($perfil->nome);
            if (
                $perfil->nivel === Perfil::NIVEL_REGIAO
                && str_contains($nomeNormalizado, 'administrador')
                && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_CRIE)
                && !Perfil::correspondeCodigo($perfil->nome, Perfil::CODIGO_ADMINISTRADOR_SISTEMA)
            ) {
                return true;
            }
        }

        return false;
    }
}
