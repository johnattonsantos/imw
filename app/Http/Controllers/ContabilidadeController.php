<?php

namespace App\Http\Controllers;

use App\Services\ServiceContabilidade\IrrfServices;
use Illuminate\Http\Request;

class ContabilidadeController extends Controller
{
    public function irrf(Request $request)
    {
        $data = app(IrrfServices::class)->execute($request->all());
        
        if($request->route()->uri() == 'contabilidade/irrf'){
            return view('contabilidade.irrf.index-contabilidade', $data);
        }else{
            return view('contabilidade.irrf.index', $data);
        }
    }

}