<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCongregacaoRequest;
use App\Models\CongregacoesCongregacao;
use App\Services\ServiceCongregacoes\RestaurarCongregacaoService;
use App\DataTables\CongregacoesDatatable;
use App\Traits\Identifiable;
use App\Traits\LocationUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CongregacoesController extends Controller
{
    use LocationUtils, Identifiable;

    public function index()
    {
        return view('congregacoes.index');
    }

    public function list(Request $request)
    {
        try {
            return app(CongregacoesDatatable::class)->execute($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Não foi possível listar as congregações'], 500);
        }
    }

    public function novo()
    {
        try { 
            return view('congregacoes.create', ['ufs' => LocationUtils::fetchUFs()]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Não foi possível criar uma nova congregação'));
        }
    }

    public function store(SaveCongregacaoRequest $request)
    {
        try {
            DB::beginTransaction();
            CongregacoesCongregacao::create($request->all());
            DB::commit();
            return redirect()->route('congregacao.index')->with('success', __('Nova congregação salva com sucesso'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('Não foi possível salvar a congregação'));
        }
    }

    public function editar(CongregacoesCongregacao $congregacao)
    {
        try {
            return view('congregacoes.edit', [
                'congregacao' => $congregacao,
                'ufs'         => LocationUtils::fetchUFs(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Não foi possível editar a congregação'));
        }
    }

    public function update(SaveCongregacaoRequest $request, CongregacoesCongregacao $congregacao)
    {
        try {
            DB::beginTransaction();
            $congregacao->update($request->all());
            DB::commit();
            return redirect()->route('congregacao.index')->with('success', __('A congregação foi atualizada com sucesso'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('Não foi possível atualizar a congregação'));
        }
    }

    public function desativar(CongregacoesCongregacao $congregacao)
    {
        try {
            DB::beginTransaction();
            $congregacao->delete();
            DB::commit();
            return redirect()->route('congregacao.index')->with('success', __('A congregação foi desativada com sucesso'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Não foi possível desativar a congregação'));
        }
    }

    public function restaurar($id)
    {
        try {
            DB::beginTransaction();
            app(RestaurarCongregacaoService::class)->execute($id);
            DB::commit();
            return redirect()->route('congregacao.index')->with('success', __('A congregação foi restaurada com sucesso'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Não foi possível restaurar a congregação'));
        }
    }
}
