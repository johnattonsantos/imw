<?php

namespace App\Http\Controllers;

use App\Services\ServiceRegiaoRelatorios\GestaoAcessosService;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GestaoAcessosController extends Controller
{
    public function index(Request $request, GestaoAcessosService $service)
    {
        abort_unless($request->user() && $request->user()->podeVerGestaoAcessos(), 403);
        $data = $request->validate([
            'ano' => 'nullable|integer|min:2000|max:'.(now()->year + 1),
            'mes' => 'nullable|integer|min:0|max:12', 'dia' => 'nullable|integer|min:0|max:31',
            'usuario' => 'nullable|string|max:150',
        ]);
        $ano = (int) ($data['ano'] ?? now()->year);
        $mes = (int) ($data['mes'] ?? now()->month);
        $dia = (int) ($data['dia'] ?? 0);
        $usuario = trim($data['usuario'] ?? '');
        if ($dia && (!$mes || !checkdate($mes, $dia, $ano))) {
            throw \Illuminate\Validation\ValidationException::withMessages(['dia' => 'Selecione um mês e um dia válido.']);
        }
        $inicio = Carbon::create($ano, $mes ?: 1, $dia ?: 1)->startOfDay();
        $fim = $dia ? $inicio->copy() : ($mes ? $inicio->copy()->endOfMonth() : $inicio->copy()->endOfYear());
        $regiao = Identifiable::fetchtSessionRegiao();
        $monitoramento = DB::table('gestao_acessos_configuracao')->where('id', 1)->value('inicio_sessoes');
        $quadros = ['perfis' => $service->sessoes($regiao->id, $inicio->toDateString(), $fim->toDateString(), $usuario)];
        return response()->view('regiao.relatorios.gestao-acessos', compact('regiao', 'ano', 'mes', 'dia', 'usuario', 'inicio', 'fim', 'monitoramento', 'quadros'))
            ->header('Cache-Control', 'no-store, private');
    }
}
