<?php

namespace App\Http\Controllers;

use App\Services\ServiceContabilidade\IrrfPorIgrejaService;
use App\Traits\Identifiable;
use Illuminate\Http\Request;

class IrrfPorIgrejaController extends Controller
{
    public function index(Request $request, IrrfPorIgrejaService $service)
    {
        $periodo = $request->validate([
            'data_inicial' => ['nullable', 'date_format:Y-m-d'],
            'data_final' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $inicio = $periodo['data_inicial'] ?? today()->startOfMonth()->toDateString();
        $fim = $periodo['data_final'] ?? today()->toDateString();
        if ($inicio > $fim) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'data_final' => 'A data final deve ser igual ou posterior à data inicial.',
            ]);
        }

        $regiao = Identifiable::fetchtSessionRegiao();
        $igrejas = $service->listar((int) $regiao->id, $inicio, $fim);
        $totais = [];
        foreach (['retido', 'repassado', 'diferenca', 'retido_sem_clerigo', 'repassado_sem_clerigo', 'quantidade_sem_clerigo'] as $campo) {
            $totais[$campo] = $igrejas->sum($campo);
        }

        return view('contabilidade.irrf.por-igreja', compact('regiao', 'igrejas', 'inicio', 'fim', 'totais'));
    }
}
