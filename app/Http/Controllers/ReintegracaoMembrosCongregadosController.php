<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReintegracaoMembrosCongregadosRequest;
use App\Models\MembresiaMembro;
use App\Services\ServiceMembros\ReintegracaoMembrosCongregadosService;
use Illuminate\Http\Request;

class ReintegracaoMembrosCongregadosController extends Controller
{
    public function index(Request $request)
    {
        $data = app(ReintegracaoMembrosCongregadosService::class)->dadosTela($request->input('cpf'));

        return view('reintegracao-membros-congregados.index', $data);
    }

    public function store(StoreReintegracaoMembrosCongregadosRequest $request)
    {
        try {
            $destino = app(ReintegracaoMembrosCongregadosService::class)->reintegrar($request->validated());
            $route = $destino === MembresiaMembro::VINCULO_MEMBRO ? 'membro.editar' : 'congregado.editar';
            $message = $destino === MembresiaMembro::VINCULO_MEMBRO
                ? __('Membro reintegrado com sucesso!')
                : __('Congregado reintegrado com sucesso!');

            return redirect()->route($route, ['id' => $request->input('membro_id')])->with('success', $message);
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->withInput()->with('error', $e->getMessage() ?: __('Erro ao tentar reintegrar esta pessoa.'));
        }
    }
}
