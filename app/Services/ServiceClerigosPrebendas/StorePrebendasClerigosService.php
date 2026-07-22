<?php

namespace App\Services\ServiceClerigosPrebendas;


use App\Models\Prebenda;

class StorePrebendasClerigosService
{
    public function execute($request)
    {
        Prebenda::where('ano', $request->ano)->update(['ativo' => 0]);

        // Cria uma nova prebenda
        Prebenda::create([
            'ano' => $request['ano'],
            'valor' => $request['valor'],
            'ativo' => 1,
        ]);
    }
}
