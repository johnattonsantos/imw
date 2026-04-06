<?php

namespace App\Services\ServicesUsuarios;

use App\Models\InstituicoesInstituicao;
use App\Models\PerfilUser;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminEditarUsuarioService
{
    public function execute($data, $id)
    {
        $user = User::findOrFail($id);
        $instituicoes = array_values(array_filter($data['instituicao_id'] ?? [], fn ($value) => !empty($value)));
        if (empty($instituicoes)) {
            throw new \Exception('É necessário selecionar ao menos uma instituição.');
        }

        $regioesSelecionadas = $this->resolveRegionIdsByInstitutions($instituicoes);
        $regiaoUsuario = count($regioesSelecionadas) === 1 ? (int) $regioesSelecionadas[0] : null;

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf'  => preg_replace('/[^0-9]/', '', $data['cpf']),
            'telefone' => preg_replace('/[^0-9]/', '', $data['telefone']),
            'pessoa_id' => $data['pessoa_id'] ?? null,
            'regiao_id' => $regiaoUsuario,
        ]);

        if (isset($data['password']) && !empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        PerfilUser::where('user_id', $user->id)->delete();

        foreach ($data['perfil_id'] as $key => $perfilId) {
            PerfilUser::create([
                'user_id' => $user->id,
                'perfil_id' => $perfilId,
                'instituicao_id' => $data['instituicao_id'][$key],
            ]);
        }
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
}
