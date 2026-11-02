<?php

namespace App\Services\ServiceIgrejas;
use App\Traits\GetIgrejasAll;
class GetIgrejas
{

    public function execute(array $params = [])
    {
        $somenteAtivas = $params['somente_ativas'] ?? true;
        $data = GetIgrejasAll::fetchIgrejas($somenteAtivas);
        return $data;
    }
}