<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedRouteException extends Exception
{
    public function render()
    {
        return redirect()
            ->route('dashboard')
            ->with('error', __('Você não tem permissão para acessar esta página'));
    }
}
