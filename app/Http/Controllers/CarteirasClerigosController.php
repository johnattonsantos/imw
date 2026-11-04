<?php

namespace App\Http\Controllers;

use App\Services\ServiceClerigosRegiao\CarteirasClerigosService;
use App\Support\CredencialClerigo;
use App\Traits\Identifiable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CarteirasClerigosController extends Controller
{
    public function index(CarteirasClerigosService $service)
    {
        $regiao = Identifiable::fetchtSessionRegiao();

        return view('clerigos.carteiras.index', [
            'regiao' => $regiao,
            'clerigos' => $service->listar($regiao->id),
        ]);
    }

    public function pdf(Request $request, CarteirasClerigosService $service)
    {
        $data = $request->validate([
            'clerigos' => 'required|array|min:1',
            'clerigos.*' => 'required|integer|min:1|distinct',
        ], ['clerigos.required' => 'Selecione ao menos um clérigo para imprimir.']);
        $regiao = Identifiable::fetchtSessionRegiao();
        $clerigos = $service->listar($regiao->id, $data['clerigos']);
        abort_unless($clerigos->count() === count($data['clerigos']), 403, 'Há clérigos indisponíveis para impressão na região logada.');

        return Pdf::loadView('clerigos.carteiras.pdf', [
            'carteiras' => $service->preparar($clerigos, $regiao),
            'background' => $service->background(),
            'validade' => CredencialClerigo::VALIDADE,
        ])->setPaper('a4', 'portrait')->stream('carteiras-clerigos-'.now()->format('YmdHis').'.pdf');
    }
}
