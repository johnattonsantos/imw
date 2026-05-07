<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdClasseRequest;
use App\Http\Requests\UpdateEbdClasseRequest;
use App\Models\EbdClasse;
use App\Traits\Identifiable;

class EbdClasseController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $classes = EbdClasse::where('igreja_id', $igrejaId)
            ->orderByDesc('ativo')
            ->orderBy('nome')
            ->paginate(20);

        return view('ebd.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('ebd.classes.create');
    }

    public function store(StoreEbdClasseRequest $request)
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        EbdClasse::create([
            ...$request->validated(),
            'igreja_id' => $igrejaId,
        ]);

        return redirect()->route('ebd.classes.index')->with('success', 'Classe cadastrada com sucesso.');
    }

    public function edit(EbdClasse $class)
    {
        $this->authorizeByIgreja($class);

        return view('ebd.classes.edit', ['classe' => $class]);
    }

    public function update(UpdateEbdClasseRequest $request, EbdClasse $class)
    {
        $this->authorizeByIgreja($class);

        $class->update($request->validated());

        return redirect()->route('ebd.classes.index')->with('success', 'Classe atualizada com sucesso.');
    }

    public function destroy(EbdClasse $class)
    {
        $this->authorizeByIgreja($class);

        $class->delete();

        return redirect()->route('ebd.classes.index')->with('success', 'Classe removida com sucesso.');
    }

    private function authorizeByIgreja(EbdClasse $class): void
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        if ((int) $class->igreja_id !== (int) $igrejaId) {
            abort(403);
        }
    }
}
