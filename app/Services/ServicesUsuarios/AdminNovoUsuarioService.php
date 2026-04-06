<?php

namespace App\Services\ServicesUsuarios;

use App\Models\Perfil;

class AdminNovoUsuarioService
{

    public function execute()
    {
        return Perfil::query()
            ->orderBy('nome', 'asc')
            ->get();
    }
}
