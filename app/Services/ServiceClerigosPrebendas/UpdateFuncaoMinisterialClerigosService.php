<?php

namespace App\Services\ServiceClerigosPrebendas;

use App\Models\PessoaFuncaoministerial;
use App\Models\Prebenda;

class UpdateFuncaoMinisterialClerigosService
{
    public function execute($request, $id)
    {
        $funcao = PessoaFuncaoministerial::findOrFail($id);

        $funcao->update([
            'qtd_prebendas' => $request->input('qtd_prebendas'),
            'vinculo' => $request->input('vinculo') ?: null,
        ]);
    }
}
