<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitanteRequest;
use App\Http\Requests\UpdateVisitanteRequest;
use App\Rules\ValidDateOfBirth;
use App\DataTables\VisitantesDatatable;
use App\Services\ServiceVisitantes\DeletarVisitanteService;
use App\Services\ServiceVisitantes\EditarVisitanteService;
use App\Services\ServiceVisitantes\IdentificaDadosIndexService;
use App\Services\ServiceVisitantes\StoreVisitanteService;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitantesController extends Controller
{
    use Identifiable;

    public function index(Request $request)
    {
        $data = app(IdentificaDadosIndexService::class)->execute($request->all());
        return view('visitantes.index', $data);
    }

    public function list(Request $request)
    {
        try {
            return app(VisitantesDatatable::class)->execute($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'erro ao carregar os dados dos visitantes'], 500);
        }
    }

    public function update(UpdateVisitanteRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(EditarVisitanteService::class)->execute($id, $request->all());
            DB::commit();
            return redirect()->route('visitante.editar', ['id' => $id])->with('success', __('Visitante atualizado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('visitante.editar', ['id' => $id])->with('error', __('Falha ao atualizar o visitante.'));
        }
    }

    public function editar($id)
    {
        $data['visitante'] = app(EditarVisitanteService::class)->findOne($id);
        $data['gceus']= Identifiable::fetchGceus();
        $data['congregacoes'] = Identifiable::fetchCongregacoes();
        if (!$data['visitante']) {
            return redirect()->route('visitante.index')->with('error', __('Visitante não encontrado.'));
        }
        return view('visitantes.editar', $data);
    }

    public function deletar($id)
    {
        try {
            app(DeletarVisitanteService::class)->execute($id);
            return redirect()->route('visitante.index')->with('success', __('Visitante deletado com sucesso.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Falha ao deletar o visitante.'));
        }
    }

    public function novo()
    {
        try {
            $data['gceus']= Identifiable::fetchGceus();
            $data['congregacoes'] = Identifiable::fetchCongregacoes();
            return view('visitantes.create', $data);
        } catch (\Exception $e) {
            return back()->with('error', __('Falha ao abrir a página de novo visitante'));
        }
    }

    public function store(StoreVisitanteRequest $request)
    {
        try {
            DB::beginTransaction();
            app(StoreVisitanteService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('visitante.index')->with('success', __('Visitante cadastrado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('visitante.index')->with('error', $e->getMessage());
        }
    }
}