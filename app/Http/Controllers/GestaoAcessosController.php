<?php

namespace App\Http\Controllers;

use App\Services\ServiceRegiaoRelatorios\GestaoAcessosService;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GestaoAcessosController extends Controller
{
    public function index(Request $request, GestaoAcessosService $service)
    {
        abort_unless($request->user() && $request->user()->podeVerGestaoAcessos(), 403);
        $buscar = $request->boolean('buscar');
        $data = $request->validate([
            'buscar' => 'nullable|boolean',
            'data_inicial' => ($buscar ? 'required' : 'nullable').'|date_format:Y-m-d',
            'data_final' => ($buscar ? 'required' : 'nullable').'|date_format:Y-m-d|after_or_equal:data_inicial',
            'usuario' => 'nullable|string|max:150',
            'instituicao_id' => 'nullable|integer|min:1',
            'draw' => 'nullable|integer|min:0', 'start' => 'nullable|integer|min:0|max:1000000',
            'length' => 'nullable|integer|in:10,25,50,100',
            'search' => 'nullable|array', 'search.value' => 'nullable|string|max:150',
            'order' => 'nullable|array|max:1', 'order.0.column' => 'required_with:order|integer|between:0,3',
            'order.0.dir' => 'required_with:order|in:asc,desc',
        ]);
        if ($buscar && Carbon::parse($data['data_inicial'])->diffInDays(Carbon::parse($data['data_final'])) > 30) {
            throw ValidationException::withMessages(['data_final' => 'Selecione um período de no máximo 31 dias.']);
        }
        $regiao = Identifiable::fetchtSessionRegiao();
        if (!empty($data['instituicao_id']) && !$service->instituicaoPermitida($regiao->id, (int) $data['instituicao_id'])) {
            throw ValidationException::withMessages(['instituicao_id' => 'Selecione uma instituição da região.']);
        }
        if ($request->expectsJson()) {
            abort_unless($buscar, 422, 'Confirme os filtros para consultar o histórico.');
            return response()->json($service->datatable($regiao->id, $data, $data))
                ->header('Cache-Control', 'no-store, private');
        }

        $filtros = [
            'data_inicial' => $data['data_inicial'] ?? now()->startOfMonth()->toDateString(),
            'data_final' => $data['data_final'] ?? now()->toDateString(),
            'usuario' => trim($data['usuario'] ?? ''), 'instituicao_id' => $data['instituicao_id'] ?? '',
        ];
        $instituicoes = $service->instituicoes($regiao->id);
        return response()->view('regiao.relatorios.gestao-acessos', compact('regiao', 'filtros', 'buscar', 'instituicoes'))
            ->header('Cache-Control', 'no-store, private');
    }
}
