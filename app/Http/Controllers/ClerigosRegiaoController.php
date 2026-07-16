<?php

namespace App\Http\Controllers;

use App\Exceptions\PessoaNotFoundException;
use App\Models\Formacao;
use Illuminate\Http\Request;
use App\Models\PessoasPessoa;
use App\Traits\LocationUtils;
use App\Http\Requests\StoreReceberNovoClerigoRequest;
use App\Models\PessoaStatus;

;
use App\Services\ServiceClerigosRegiao\AtivarClerigoService;
use App\Services\ServiceClerigosRegiao\BuscarClerigoPorCpfService;
use App\Services\ServiceClerigosRegiao\ListaClerigosService;
use App\Services\ServiceClerigosRegiao\StoreClerigosService;
use App\Services\ServiceClerigosRegiao\DeletarClerigoService;
use App\Services\ServiceClerigosRegiao\UpdateClerigosService;
use App\Services\ServiceClerigosRegiao\DetalhesClerigoService;
use App\Services\ServiceClerigosRegiao\EditarClerigoService;

class ClerigosRegiaoController extends Controller
{

    use LocationUtils;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');
        $clerigos = app(ListaClerigosService::class)->execute($searchTerm);
        return view('clerigos.index', compact('clerigos'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function novo()
    {
        $formacoes =  Formacao::all();
        $ufs = $this->fetchUFs();
        $situacoes =  PessoaStatus::all();
        return view('clerigos.novo.index', compact('ufs', 'formacoes', 'situacoes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReceberNovoClerigoRequest $request)
    {
        app(StoreClerigosService::class)->execute($request);

        return redirect()->route('clerigos.index')->with('success', __('Clerigo criado com sucesso!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        $formacoes =  Formacao::all();
        $situacoes =  PessoaStatus::all();
        $ufs = $this->fetchUFs();
        $clerigo = app(EditarClerigoService::class)->findOne($id);
        //$clerigo = PessoasPessoa::findOrfail($id);
        return view('clerigos.editar.index', compact('clerigo', 'ufs', 'formacoes', 'situacoes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreReceberNovoClerigoRequest $request, $id)
    {
        app(UpdateClerigosService::class)->execute($request, $id);

        return redirect()->route('clerigos.index')->with('success', __('Clerigo editado com sucesso!'));
    }

    public function deletar($id)
    {
        app(DeletarClerigoService::class)->execute($id);

        return redirect()->route('clerigos.index')->with('success', __('Clerigo inativado com sucesso.'));
    }

    public function ativar($id)
    {
        app(AtivarClerigoService::class)->execute($id);

        return redirect()->route('clerigos.index')->with('success', __('Clerigo ativado com sucesso.'));
    }

    public function detalhes($id)
    {
        $clerigos = app(DetalhesClerigoService::class)->execute($id);

        return response()->json($clerigos);
    }

    public function findByCpf(string $cpf)
    {
        try {
            $clerigo = app(BuscarClerigoPorCpfService::class)->execute($cpf);

            return response()->json(['clerigo' => $clerigo]);
        } catch (PessoaNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
