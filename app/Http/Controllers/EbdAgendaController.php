<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdAgendaRequest;
use App\Http\Requests\UpdateEbdAgendaRequest;
use App\Models\EbdAgenda;
use App\Models\EbdTurma;
use App\Traits\Identifiable;
use Illuminate\Validation\ValidationException;

class EbdAgendaController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $agendas = EbdAgenda::with('turma.professor.membro')
            ->where(function ($query) use ($igrejaId) {
                $query->whereNull('turma_id')
                    ->orWhereHas('turma.professor.membro', fn ($q) => $q->where('igreja_id', $igrejaId));
            })
            ->orderByDesc('data_inicio')
            ->paginate(20);

        return view('ebd.agendas.index', compact('agendas'));
    }

    public function create()
    {
        return view('ebd.agendas.create', [
            'turmas' => $this->turmasDaIgreja(),
        ]);
    }

    public function store(StoreEbdAgendaRequest $request)
    {
        $data = $request->validated();

        $this->validateTurma($data['turma_id'] ?? null);

        EbdAgenda::create($data);

        return redirect()->route('ebd.agendas.index')->with('success', 'Evento cadastrado com sucesso.');
    }

    public function edit(EbdAgenda $agenda)
    {
        $this->authorizeByIgreja($agenda);

        return view('ebd.agendas.edit', [
            'agenda' => $agenda,
            'turmas' => $this->turmasDaIgreja(),
        ]);
    }

    public function update(UpdateEbdAgendaRequest $request, EbdAgenda $agenda)
    {
        $this->authorizeByIgreja($agenda);

        $data = $request->validated();
        $this->validateTurma($data['turma_id'] ?? null);

        $agenda->update($data);

        return redirect()->route('ebd.agendas.index')->with('success', 'Evento atualizado com sucesso.');
    }

    public function destroy(EbdAgenda $agenda)
    {
        $this->authorizeByIgreja($agenda);

        $agenda->delete();

        return redirect()->route('ebd.agendas.index')->with('success', 'Evento removido com sucesso.');
    }

    private function turmasDaIgreja()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        return EbdTurma::with(['classe', 'professor.membro'])
            ->whereHas('professor.membro', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get();
    }

    private function validateTurma(?int $turmaId): void
    {
        if (! $turmaId) {
            return;
        }

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $exists = EbdTurma::where('id', $turmaId)
            ->whereHas('professor.membro', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'turma_id' => 'Turma inválida para esta igreja.',
            ]);
        }
    }

    private function authorizeByIgreja(EbdAgenda $agenda): void
    {
        if (! $agenda->turma_id) {
            return;
        }

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        if ((int) $agenda->turma?->professor?->membro?->igreja_id !== (int) $igrejaId) {
            abort(403);
        }
    }
}
