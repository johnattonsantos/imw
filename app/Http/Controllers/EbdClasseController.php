<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdClasseRequest;
use App\Http\Requests\UpdateEbdClasseRequest;
use App\Models\EbdClasse;

class EbdClasseController extends Controller
{
    public function index()
    {
        $classes = EbdClasse::orderByDesc('ativo')->orderBy('nome')->paginate(20);

        return view('ebd.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('ebd.classes.create');
    }

    public function store(StoreEbdClasseRequest $request)
    {
        EbdClasse::create($request->validated());

        return redirect()->route('ebd.classes.index')->with('success', 'Classe cadastrada com sucesso.');
    }

    public function edit(EbdClasse $class)
    {
        return view('ebd.classes.edit', ['classe' => $class]);
    }

    public function update(UpdateEbdClasseRequest $request, EbdClasse $class)
    {
        $class->update($request->validated());

        return redirect()->route('ebd.classes.index')->with('success', 'Classe atualizada com sucesso.');
    }

    public function destroy(EbdClasse $class)
    {
        $class->delete();

        return redirect()->route('ebd.classes.index')->with('success', 'Classe removida com sucesso.');
    }
}
