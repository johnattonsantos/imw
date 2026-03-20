<?php

namespace App\Services\ServiceDistritoRelatorios;


use App\Traits\AspirantesIgrejasDadosDistrito;

class AspirantesIgrejasService
{

    public function execute(array $params = [])
    {
        $data =  [
            'aspirantes' => collect(),
        ];

        $params['distritoId'] = session()->get('session_perfil')->instituicao_id;
        if(isset($params['action'])) {
            $data =  [
                'aspirantes'   => AspirantesIgrejasDadosDistrito::fetchAspirantesPorIgrejas($params),
            ];
        }
        return $data;
    }
}
