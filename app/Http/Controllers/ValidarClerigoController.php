<?php

namespace App\Http\Controllers;

use App\Support\CredencialClerigo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidarClerigoController extends Controller
{
    public function show(Request $request)
    {
        $params = $request->validate(['clerigo' => 'required|integer|min:1']);
        $clerigo = DB::table('pessoas_pessoas as pessoa')
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'pessoa.regiao_id')
            ->leftJoin('pessoas_status as situacao', 'situacao.id', '=', 'pessoa.situacao_id')
            ->where('pessoa.id', $params['clerigo'])
            ->select([
                'pessoa.nome', 'pessoa.rol', 'pessoa.categoria', 'pessoa.deleted_at',
                'regiao.nome as regiao_nome', 'situacao.descricao as situacao_nome',
            ])->first();

        return response()->view('perfil.validar-clerigo', [
            'clerigo' => $clerigo,
            'validade' => Carbon::parse(CredencialClerigo::VALIDADE),
            'expirada' => Carbon::today()->gt(Carbon::parse(CredencialClerigo::VALIDADE)),
        ], $clerigo ? 200 : 404)->header('Cache-Control', 'no-store, private');
    }
}
