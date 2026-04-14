<?php

namespace App\Http\Middleware;

use App\Models\PerfilUser;
use App\Services\ServicePerfil\IdentificaPerfilService;
use Closure;
use Illuminate\Http\Request;

class VerificaPerfil
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se a session 'perfil' não existe
        if (!session()->has('session_perfil')) {
            $user = auth()->user();

            // Obtém a contagem de instituicoes vinculadas ao usuário
          
            $countPerfil = PerfilUser::where('user_id', $user->id)->count();

            // Se o usuário está vinculado a apenas uma instituicao
            if ($countPerfil == 1) {
                $perfil = PerfilUser::where('user_id', $user->id)
                ->leftJoin('instituicoes_instituicoes', 'instituicoes_instituicoes.id', '=', 'perfil_user.instituicao_id')
                ->join('perfils', 'perfils.id', '=', 'perfil_user.perfil_id')
                ->select('instituicoes_instituicoes.id as instituicao_id',
                    'instituicoes_instituicoes.nome as instituicao_nome',
                    'perfils.id as perfil_id', 
                    'perfils.nome as perfil_nome')
                ->first();
                
                $perfil = app(IdentificaPerfilService::class)->execute(
                    $perfil->instituicao_id, 
                    $perfil->instituicao_nome,
                    $perfil->perfil_id,
                    $perfil->perfil_nome,
                );

                session([
                    'session_perfil' => $perfil
                ]);

            } else {
                // Se o usuário estiver vinculado a mais de uma redireciona para selecionarInstituicao
                return redirect('selecionarPerfil');
            }
        }

        return $next($request);
    }
}
