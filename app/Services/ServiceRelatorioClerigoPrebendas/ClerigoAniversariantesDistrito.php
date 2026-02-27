<?php

namespace App\Services\ServiceRelatorioClerigoPrebendas;


use App\Traits\ClerigoPrebenda;
use App\Traits\Identifiable;

class ClerigoAniversariantesDistrito
{
    use Identifiable;


    public function execute(array $params = [])
    {
        $distrito = session()->get('session_perfil')->instituicao_id;
        $data =  [];
        if(isset($params['action'])) {
            $data =  [
                'aniversariantes'   => ClerigoPrebenda::fetchClerigoAniversarinatesDistrito($distrito, $params),
                'mes'               => ''
            ];
        }
        return $data;
    }
}
