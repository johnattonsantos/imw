<?php

namespace App\Http\Middleware;

use App\Models\Perfil;
use Closure;
use Illuminate\Http\Request;

class CheckCrieRegionalScope
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $perfilNome = (string) optional(session('session_perfil'))->perfil_nome;
        $isCrie = Perfil::correspondeCodigo($perfilNome, Perfil::CODIGO_CRIE);

        if (!$isCrie) {
            return $next($request);
        }

        $userRegiaoId = (int) optional(auth()->user())->regiao_id;
        $sessionRegiaoId = (int) data_get(session('session_perfil'), 'instituicoes.regiao.id');
        if ($sessionRegiaoId <= 0) {
            $sessionRegiaoId = (int) data_get(session('session_perfil'), 'instituicao_id');
        }

        if ($userRegiaoId <= 0 || $sessionRegiaoId <= 0 || $userRegiaoId !== $sessionRegiaoId) {
            return redirect()->route('dashboard')
                ->with('error', 'Perfil CRIE só pode acessar dados da própria região.');
        }

        return $next($request);
    }
}

