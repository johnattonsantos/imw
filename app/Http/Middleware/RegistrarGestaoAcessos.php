<?php

namespace App\Http\Middleware;

use App\Services\ServiceRegiaoRelatorios\RegistrarAcessoService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistrarGestaoAcessos
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $autenticada = $request->route() && in_array('auth', $request->route()->gatherMiddleware(), true);
        if ($autenticada && $request->isMethod('GET') && !$request->ajax() && !$request->expectsJson()
            && !$request->is('selecionarPerfil', 'postPerfil')
            && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            try {
                app(RegistrarAcessoService::class)->registrar($request);
            } catch (\Throwable $e) {
                // A monitoring failure must not prevent use of the church management system.
                Log::warning('Falha no registro de Gestao de Acessos.', ['exception' => get_class($e)]);
            }
        }
        return $response;
    }
}
