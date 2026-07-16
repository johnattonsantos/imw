<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDependenteRequest;
use App\Models\PessoasDependente;
use App\Services\ServiceDependentes\DeleteDependenteService;
use App\Services\ServiceDependentes\StoreDependenteService;
use App\Services\ServiceDependentes\UpdateDependenteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClerigoPerfilController extends Controller
{
    public function indexDependentes(Request $request)
    {
        return view('perfil.clerigos.dependentes.index', [
            'dependentes' => $request->user()->pessoa->dependentes
        ]);
    }

    public function createDependente()
    {
        return view('perfil.clerigos.dependentes.create');
    }

    public function storeDependente(StoreDependenteRequest $request)
    {
        try {
            DB::beginTransaction();
            app(StoreDependenteService::class)->execute($request->user()->pessoa_id, $request->validated());
            DB::commit();
            
            return redirect()->route('clerigos.perfil.dependentes.index')->with('success', __('Dependente cadastrado com sucesso.'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->route('clerigos.perfil.dependentes.create')->with('error', __('Houve um erro ao tentar cadastrar um dependente, por favor tente novamente'));
        }
    }

    public function editDependente(PessoasDependente $dependente)
    {
        return view('perfil.clerigos.dependentes.edit', compact('dependente'));
    }

    public function updateDependente(PessoasDependente $dependente, StoreDependenteRequest $request)
    {
        try {
            DB::beginTransaction();
            app(UpdateDependenteService::class)->execute($dependente, $request->validated());
            DB::commit();
            
            return redirect()->route('clerigos.perfil.dependentes.index')->with('success', __('Dependente atualizado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('Houve um erro ao tentar cadastrar um dependente, por favor tente novamente'));
        }
    }

    public function deleteDependente(PessoasDependente $dependente)
    {
        try {
            app(DeleteDependenteService::class)->execute($dependente);

            return redirect()->route('clerigos.perfil.dependentes.index')->with('success', __('Dependente excluído com sucesso.'));
        } catch (\Exception $e) {
            return back()->with('error', __('Houve um erro ao tentar apagar os dados do dependente, por favor tente novamente'));
        }
    }
}
