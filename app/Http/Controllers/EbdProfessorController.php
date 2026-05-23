<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdProfessorRequest;
use App\Http\Requests\UpdateEbdProfessorRequest;
use App\Models\EbdProfessor;
use App\Models\EbdTurma;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Http\Request;
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
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $membros = MembresiaMembro::query()
            ->where('igreja_id', $igrejaId)
            ->where('status', MembresiaMembro::STATUS_ATIVO)
            ->where('vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->orderBy('nome')
            ->get(['id', 'nome', 'vinculo']);

        $instituicao = Identifiable::fetchSessionIgrejaLocal()->nome;

        return view('ebd.professores.create', compact('membros', 'instituicao'));
    }

    public function store(StoreEbdProfessorRequest $request)
    {
        $data = $request->validated();
        $this->validateNoDuplicate($data);

        $professor = EbdProfessor::create($data);

        return redirect()
            ->route('ebd.professores.vinculos', $professor->id)
            ->with('success', 'Professor vinculado com sucesso. Agora escolha a EBD para inclusão.');
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

        if ($professore->turmas()->exists()) {
            return redirect()
                ->route('ebd.professores.index')
                ->with('error', 'Não é possível remover o professor, pois existem turmas vinculadas.');
        }

        $professore->delete();

        return redirect()->route('ebd.professores.index')->with('success', 'Professor removido com sucesso.');
    }

    public function vinculos(EbdProfessor $professore)
    {
        $this->authorizeByIgreja($professore);

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $turmas = EbdTurma::with(['classe', 'professor.membro'])
            ->where('ativo', true)
            ->whereHas('classe', fn ($q) => $q->where('igreja_id', $igrejaId)->where('ativo', true))
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get();

        $ebdsDoProfessor = EbdTurma::with('classe')
            ->where('professor_id', $professore->id)
            ->whereHas('classe', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get();

        return view('ebd.professores.vinculos', [
            'professor' => $professore,
            'turmas' => $turmas,
            'ebdsDoProfessor' => $ebdsDoProfessor,
            'instituicao' => Identifiable::fetchSessionIgrejaLocal()->nome,
        ]);
    }

    public function vincularTurma(Request $request, EbdProfessor $professore)
    {
        $this->authorizeByIgreja($professore);

        $data = $request->validate([
            'turma_id' => ['required', 'integer', 'exists:ebd_turmas,id'],
        ]);

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $turma = EbdTurma::with('professor.membro')
            ->where('id', (int) $data['turma_id'])
            ->where('ativo', true)
            ->whereHas('classe', fn ($q) => $q->where('igreja_id', $igrejaId)->where('ativo', true))
            ->first();

        if (! $turma) {
            return redirect()->back()->with('error', 'EBD inválida para a igreja logada.');
        }

        if ((int) $turma->professor_id === (int) $professore->id) {
            return redirect()->back()->with('success', 'Esse professor já está vinculado nessa EBD.');
        }

        $nomeProfessorAnterior = $turma->professor?->membro?->nome;
        $turma->update(['professor_id' => $professore->id]);

        if ($nomeProfessorAnterior) {
            return redirect()->back()->with('success', 'Professor vinculado com sucesso. A EBD estava com ' . $nomeProfessorAnterior . '.');
        }

        return redirect()->back()->with('success', 'Professor vinculado na EBD com sucesso.');
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
