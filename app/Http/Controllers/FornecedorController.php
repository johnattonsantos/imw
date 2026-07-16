<?php

namespace App\Http\Controllers;

use App\Exceptions\FornecedorNotFoundException;
use App\Http\Requests\StoreFornecedorRequest;
use App\Http\Requests\UpdateFornecedorRequest;
use App\Models\FinanceiroFornecedores;
use App\Services\ServiceFonecedor\DeletarFornecedorService;
use App\Services\ServiceFonecedor\ListFornecedorService;
use App\Services\ServiceFonecedor\SalvarFornecedorService;
use App\Services\ServiceFonecedor\UpdateFornecedorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FornecedorController extends Controller
{
    public function index(Request $request)
    {
        $data = app(ListFornecedorService::class)->execute($request->all());
        return view('financeiro.fornecedores.index', $data);
    }


    public function novo(){
        return view('financeiro.fornecedores.novo');
    }  

    public function store(StoreFornecedorRequest $request)
    {
        try {
            DB::beginTransaction();
            app(SalvarFornecedorService::class)->execute($request->all());
            DB::commit();
            return redirect()->route('fornecedor.novo')->with('success', __('Fornecedor cadastrado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('fornecedor.novo')->with('error', $e->getMessage());
        }
    }

    public function deletar($id) 
    {
        try {
            DB::beginTransaction();
            app(DeletarFornecedorService::class)->execute($id);
            DB::commit();
            return redirect()->route('fornecedor.index')->with('success', __('Fornecedor excluído com sucesso.'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->route('fornecedor.index')->with('error', $e->getMessage());
        }
    }

      
    public function editar($id)
    {
        try {
            $fornecedor = FinanceiroFornecedores::findOrFail($id);
            return view('financeiro.fornecedores.editar', compact('fornecedor', 'id'));    
        }  catch(FornecedorNotFoundException $e) {
            return redirect()->route('fornecedor.index')->with('error', __('Registro não encontrado.'));
        }
        catch (\Exception $e) {
            return redirect()->route('fornecedor.index')->with('error', __('Erro ao abrir a página, por favor, tente mais tarde!'));
        }
    }

    public function update(UpdateFornecedorRequest $request, $id)
    {   
        try {
            DB::beginTransaction();
            app(UpdateFornecedorService::class)->execute($request->all(), $id);
            DB::commit();
            return redirect()->route('fornecedor.index')->with('success', __('Fornecedor atualizado com sucesso.'));
        } catch(\Exception $e) {
            DB::rollback();
            return redirect()->action([FornecedorController::class, 'editar'], ['id' => $id])->with('error', __('Falha na atualização do registro.'));
        }
    }
}
