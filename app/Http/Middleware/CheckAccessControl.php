<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccessControl
{
    public function handle(Request $request, Closure $next, ...$regras)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Acesso não autorizado');
        }

        foreach ($regras as $regraNome) {
            if ($user->hasPerfilRegra($regraNome)) {
                return $next($request);
            }
        }

        if (empty($regras)) {
            abort(403, 'Acesso não autorizado');
        }

        abort(403, 'Acesso não autorizado');
    }
}
