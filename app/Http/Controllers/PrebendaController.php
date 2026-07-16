<?php

namespace App\Http\Controllers;

use App\Calculators\PrebendasClerigos\MaxPrebendasClerigoCalculator;
use App\Http\Requests\CreatePrebendaRequest;
use App\Http\Requests\StorePrebendaRequest;
use App\Http\Requests\UpdatePrebendaRequest;
use App\Models\PessoasPrebenda;
use App\Services\ServicePrebendas\BuscarDadosPrebendasService;
use App\Services\ServicePrebendas\CalculateMaxValorPrebendaClerigoService;
use App\Services\ServicePrebendas\DeletePrebendaService;
use App\Services\ServicePrebendas\StorePrebendasService;
use App\Services\ServicePrebendas\UpdatePrebendaService;
use App\Traits\Identifiable;
use Illuminate\Http\Request;

class PrebendaController extends Controller
{
    use Identifiable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prebendas = PessoasPrebenda::where('pessoa_id', Identifiable::fetchSessionPessoa()->id)
            ->orderBy('ano', 'desc')
            ->get();

        return view("perfil.clerigos.prebendas.index", ['prebendas' => $prebendas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = app(BuscarDadosPrebendasService::class)->execute();

        return view('perfil.clerigos.prebendas.create', $data);
    }

    public function maxPrebenda($ano)
    {
        $calculator = new MaxPrebendasClerigoCalculator();
        $valorMaxPrebenda = (new CalculateMaxValorPrebendaClerigoService($calculator))->execute($ano);

        return response()->json(['valor' => $valorMaxPrebenda]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePrebendaRequest $request)
    {
        app(StorePrebendasService::class)->execute($request);
        return redirect()->route('clerigos.perfil.prebendas.index')->with('success', __('Prebenda criada com sucesso'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prebenda = PessoasPrebenda::findOrFail($id);
        $data = app(BuscarDadosPrebendasService::class)->execute();
        return view('perfil.clerigos.prebendas.edit', [...$data, 'prebenda' => $prebenda]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePrebendaRequest $request, $id)
    {
        app(UpdatePrebendaService::class)->execute($request, $id);

        return redirect()->route('clerigos.perfil.prebendas.index')->with('success', __('Prebenda atualizada com sucesso'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        app(DeletePrebendaService::class)->execute($id);

        return redirect()->route('clerigos.perfil.prebendas.index')->with('success', __('Prebenda deletada com sucesso'));
    }
}
