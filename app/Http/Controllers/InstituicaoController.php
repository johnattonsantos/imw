<?php

namespace App\Http\Controllers;

use App\Models\InstituicoesInstituicao;
use Illuminate\Http\Request;

class InstituicaoController extends Controller
{
    public function index(Request $request)
    {
        $query = InstituicoesInstituicao::select('instituicoes_instituicoes.id', 'instituicoes_instituicoes.nome', 'instituicao_pai.nome as instituicao_pai_nome')
            ->leftJoin('instituicoes_instituicoes as instituicao_pai', 'instituicoes_instituicoes.instituicao_pai_id', '=', 'instituicao_pai.id')
            ->when($request->has('search') && !empty($request->search), function($query) use ($request) {
                $query->where('instituicoes_instituicoes.nome', 'like', '%' . $request->search . '%');
            })
            ->where('instituicoes_instituicoes.ativo', 1)
            ->orderBy('instituicoes_instituicoes.nome', 'asc');


        $instituicoes = $query->paginate(10);
        return response()->json($instituicoes);
    }
    
    public function instituicoesLocais(Request $request)
    {
        $query = InstituicoesInstituicao::when($request->has('search') && !empty($request->search), function($query) use ($request) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        });

        $query->where('id', session()->get('session_perfil')->instituicao_id);

        $instituicoes = $query->paginate(10);
        return response()->json($instituicoes);
    }

}
