<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdProfessorRequest;
use App\Http\Requests\UpdateEbdProfessorRequest;
use App\Models\EbdProfessor;
use App\Traits\Identifiable;
use Illuminate\Validation\ValidationException;

class EbdProfessorController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $professores = EbdProfessor::with('membro')
            ->whereHas('membro', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('ativo')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('ebd.professores.index', compact('professores'));
    }

    public function create()
    {
        return view('ebd.professores.create');
    }

    public function store(StoreEbdProfessorRequest $request)
    {
        $data = $request->validated();
        $this->validateNoDuplicate($data);

        EbdProfessor::create($data);

        return redirect()->route('ebd.professores.index')->with('success', 'Professor vinculado com sucesso.');
    }

    public function edit(EbdProfessor $professore)
    {
        $this->authorizeByIgreja($professore);

        return view('ebd.professores.edit', ['professor' => $professore]);
    }

    public function update(UpdateEbdProfessorRequest $request, EbdProfessor $professore)
    {
        $this->authorizeByIgreja($professore);

        $data = $request->validated();
        $this->validateNoDuplicate($data, $professore->id);

        $professore->update($data);

        return redirect()->route('ebd.professores.index')->with('success', 'Professor atualizado com sucesso.');
    }

    public function destroy(EbdProfessor $professore)
    {
        $this->authorizeByIgreja($professore);

        $professore->delete();

        return redirect()->route('ebd.professores.index')->with('success', 'Professor removido com sucesso.');
    }

    private function validateNoDuplicate(array $data, ?int $ignoreId = null): void
    {
        if (! (bool) ($data['ativo'] ?? false)) {
            return;
        }

        $exists = EbdProfessor::where('membro_id', $data['membro_id'])
            ->where('ativo', true)
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'membro_id' => 'Essa pessoa já está vinculada como professor ativo.',
            ]);
        }
    }

    private function authorizeByIgreja(EbdProfessor $professor): void
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        if ((int) $professor->membro?->igreja_id !== (int) $igrejaId) {
            abort(403);
        }
    }
}
