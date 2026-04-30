<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdAlunoRequest;
use App\Http\Requests\UpdateEbdAlunoRequest;
use App\Models\EbdAluno;
use App\Traits\Identifiable;
use Illuminate\Validation\ValidationException;

class EbdAlunoController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $alunos = EbdAluno::with('membro')
            ->whereHas('membro', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('ativo')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('ebd.alunos.index', compact('alunos'));
    }

    public function create()
    {
        return view('ebd.alunos.create');
    }

    public function store(StoreEbdAlunoRequest $request)
    {
        $data = $request->validated();
        $this->validateNoDuplicate($data);

        EbdAluno::create($data);

        return redirect()->route('ebd.alunos.index')->with('success', 'Aluno vinculado com sucesso.');
    }

    public function edit(EbdAluno $aluno)
    {
        $this->authorizeByIgreja($aluno);

        return view('ebd.alunos.edit', compact('aluno'));
    }

    public function update(UpdateEbdAlunoRequest $request, EbdAluno $aluno)
    {
        $this->authorizeByIgreja($aluno);

        $data = $request->validated();
        $this->validateNoDuplicate($data, $aluno->id);

        $aluno->update($data);

        return redirect()->route('ebd.alunos.index')->with('success', 'Aluno atualizado com sucesso.');
    }

    public function destroy(EbdAluno $aluno)
    {
        $this->authorizeByIgreja($aluno);

        $aluno->delete();

        return redirect()->route('ebd.alunos.index')->with('success', 'Aluno removido com sucesso.');
    }

    private function validateNoDuplicate(array $data, ?int $ignoreId = null): void
    {
        if (! (bool) ($data['ativo'] ?? false)) {
            return;
        }

        $exists = EbdAluno::where('membro_id', $data['membro_id'])
            ->where('ativo', true)
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'membro_id' => 'Essa pessoa já está vinculada como aluno ativo.',
            ]);
        }
    }

    private function authorizeByIgreja(EbdAluno $aluno): void
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        if ((int) $aluno->membro?->igreja_id !== (int) $igrejaId) {
            abort(403);
        }
    }
}
