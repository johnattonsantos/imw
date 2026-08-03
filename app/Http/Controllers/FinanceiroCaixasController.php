<?php

namespace App\Http\Controllers;

use App\Exceptions\FinanceiroCaixaNotFoundException;
use App\Exceptions\MembroNotFoundException;
use App\Http\Requests\StoreCaixasRequest;
use App\Http\Requests\UpdateCaixasRequest;
use App\Models\FinanceiroCaixa;
use App\Services\ServiceFinanceiroCaixas\DeletarFinanceiroCaixasService;
use App\Services\ServiceFinanceiroCaixas\ListFinanceiroCaixasService;
use App\Services\ServiceFinanceiroCaixas\StoreFinanceiroCaixasService;
use App\Services\ServiceFinanceiroCaixas\UpdateFinanceiroCaixasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceiroCaixasController extends Controller
{
    public function index(Request $request) {
        $data = app(ListFinanceiroCaixasService::class)->execute($request->all());
        return view('financeiro.caixas.index', $data);
    }

    public function novo() {
        return view('financeiro.caixas.novo');
    }

    public function store(StoreCaixasRequest $request) {
        try {
            DB::beginTransaction();
            app(StoreFinanceiroCaixasService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('financeiro.caixas')->with('success', __('Caixa cadastrado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('financeiro.caixas.novo')->with('error', $e->getMessage());
        }
    }

    public function deletar($id)
    {
        try {
            DB::beginTransaction();
            app(DeletarFinanceiroCaixasService::class)->execute($id);
            DB::commit();
            return redirect()->route('financeiro.caixas')->with('success', __('Caixa excluído com sucesso.'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('financeiro.caixas')->with('error', $e->getMessage());
        }
    }


    public function editar($id)
    {
        try {
            $caixa = FinanceiroCaixa::findOrFail($id);
            return view('financeiro.caixas.editar', compact('caixa', 'id'));
        }  catch(FinanceiroCaixaNotFoundException $e) {
            return redirect()->route('financeiro.caixas')->with('error', __('Registro não encontrado.'));
        }
        catch (\Exception $e) {
            return redirect()->route('financeiro.caixas')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function update(UpdateCaixasRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            app(UpdateFinanceiroCaixasService::class)->execute($request->all(), $id);
            DB::commit();
            return redirect()->route('financeiro.caixas')->with('success', __('Caixa atualizado com sucesso.'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->action([FinanceiroCaixasController::class, 'editar'], ['id' => $id])->with('error', __('Falha na atualização do registro.'));
        }
    }

}
