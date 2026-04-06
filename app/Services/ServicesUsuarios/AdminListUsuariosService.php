<?php

namespace App\Services\ServicesUsuarios;

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
        $query = User::with(['perfilUser.perfil', 'perfilUser.instituicao'])
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

        return $query->paginate(100);
    }
}
