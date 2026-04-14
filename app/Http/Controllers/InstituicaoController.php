<?php

namespace App\Http\Controllers;

use App\Models\InstituicoesInstituicao;
use App\Models\Perfil;
use Illuminate\Http\Request;

class InstituicaoController extends Controller
{
    public function index(Request $request)
    {
        $query = InstituicoesInstituicao::select('instituicoes_instituicoes.id', 'instituicoes_instituicoes.nome', 'instituicao_pai.nome as instituicao_pai_nome')
            ->leftJoin('instituicoes_instituicoes as instituicao_pai', 'instituicoes_instituicoes.instituicao_pai_id', '=', 'instituicao_pai.id')
            ->when($request->has('search') && !empty($request->search), function($query) use ($request) {
                $query->where('instituicoes_instituicoes.nome', 'like', '%' . $request->search . '%');
            })
            ->where('instituicoes_instituicoes.ativo', 1)
            ->orderBy('instituicoes_instituicoes.nome', 'asc');

        if ($this->isCrieProfile() || $this->isRegionalAdminProfile()) {
            $regiaoId = $this->resolveCurrentRegionId();
            $query->where(function ($subquery) use ($regiaoId) {
                $subquery->where('instituicoes_instituicoes.id', $regiaoId)
                    ->orWhere('instituicoes_instituicoes.regiao_id', $regiaoId);
            });
        }

        $instituicoes = $query->paginate(10);
        return response()->json($instituicoes);
    }
    
    public function instituicoesLocais(Request $request)
    {
        $query = InstituicoesInstituicao::when($request->has('search') && !empty($request->search), function($query) use ($request) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        });

        $query->where('id', session()->get('session_perfil')->instituicao_id);

        $instituicoes = $query->paginate(10);
        return response()->json($instituicoes);
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
