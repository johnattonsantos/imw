<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdTurmaRequest;
use App\Http\Requests\UpdateEbdTurmaRequest;
use App\Models\EbdAluno;
use App\Models\EbdClasse;
use App\Models\CongregacoesCongregacao;
use App\Models\EbdProfessor;
use App\Models\EbdTurma;
use App\Models\EbdTurmaAluno;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EbdTurmaController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $turmas = EbdTurma::with(['classe', 'professor.membro'])
            ->withCount(['alunosVinculos as total_alunos_ativos' => fn ($q) => $q->where('ativo', true)])
            ->whereHas('classe', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->paginate(20);

        return view('ebd.turmas.index', compact('turmas'));
    }

    public function create()
    {
        $data = $this->formData();

        return view('ebd.turmas.create', $data);
    }

    public function store(StoreEbdTurmaRequest $request)
    {
        $data = $request->validated();
        $congregacaoId = $this->resolveCongregacaoId($data['congregacao_id'] ?? null);
        $professorId = isset($data['professor_id']) && $data['professor_id'] !== ''
            ? (int) $data['professor_id']
            : null;

        $this->validateClasseByIgreja($data['classe_id']);
        $this->validateProfessorAndAlunos($professorId, $data['alunos'] ?? []);

        DB::beginTransaction();
        try {
            $turma = EbdTurma::create([
                'classe_id' => $data['classe_id'],
                'professor_id' => $professorId,
                'congregacao_id' => $congregacaoId,
                'nome' => $data['nome'],
                'ano' => $data['ano'],
                'semestre' => $data['semestre'] ?? null,
                'ativo' => $data['ativo'],
            ]);

            if (array_key_exists('alunos', $data)) {
                $this->syncAlunos($turma, $data['alunos'] ?? []);
            }

            DB::commit();

            return redirect()->route('ebd.turmas.index')->with('success', 'Turma cadastrada com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function edit(EbdTurma $turma)
    {
        $this->authorizeByIgreja($turma);

        $data = $this->formData();
        $data['turma'] = $turma->load('alunosVinculos');
        $data['alunosAtivosVinculados'] = $turma->alunosVinculos()->where('ativo', true)->pluck('aluno_id')->toArray();

        return view('ebd.turmas.edit', $data);
    }

    public function update(UpdateEbdTurmaRequest $request, EbdTurma $turma)
    {
        $this->authorizeByIgreja($turma);

        $data = $request->validated();
        $congregacaoId = $this->resolveCongregacaoId($data['congregacao_id'] ?? null);
        $professorId = isset($data['professor_id']) && $data['professor_id'] !== ''
            ? (int) $data['professor_id']
            : null;
        $this->validateClasseByIgreja($data['classe_id']);
        $this->validateProfessorAndAlunos($professorId, $data['alunos'] ?? []);

        DB::beginTransaction();
        try {
            $turma->update([
                'classe_id' => $data['classe_id'],
                'professor_id' => $professorId,
                'congregacao_id' => $congregacaoId,
                'nome' => $data['nome'],
                'ano' => $data['ano'],
                'semestre' => $data['semestre'] ?? null,
                'ativo' => $data['ativo'],
            ]);

            if (array_key_exists('alunos', $data)) {
                $this->syncAlunos($turma, $data['alunos'] ?? []);
            }

            DB::commit();

            return redirect()->route('ebd.turmas.index')->with('success', 'Turma atualizada com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(EbdTurma $turma)
    {
        $this->authorizeByIgreja($turma);

        $totalDiarios = $turma->diarios()->count();
        $totalAgendas = DB::table('ebd_agendas')->where('turma_id', $turma->id)->count();

        if ($totalDiarios > 0 || $totalAgendas > 0) {
            $mensagens = [];

            if ($totalDiarios > 0) {
                $mensagens[] = "{$totalDiarios} diário(s)";
            }

            if ($totalAgendas > 0) {
                $mensagens[] = "{$totalAgendas} agenda(s)";
            }

            return redirect()
                ->route('ebd.turmas.index')
                ->with('error', 'Esta EBD possui ' . implode(' e ', $mensagens) . '. Ela só poderá ser deletada quando estiver sem agenda e sem diário.');
        }

        DB::beginTransaction();
        try {
            $turma->alunosVinculos()->delete();
            $turma->delete();

            DB::commit();

            return redirect()->route('ebd.turmas.index')->with('success', 'Turma removida com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('ebd.turmas.index')
                ->with('error', 'Não foi possível remover a turma. Verifique os vínculos existentes e tente novamente.');
        }
    }

    private function formData(): array
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        return [
            'classes' => EbdClasse::where('ativo', true)
                ->where('igreja_id', $igrejaId)
                ->orderBy('nome')
                ->get(),
            'professores' => EbdProfessor::with('membro')
                ->where('ativo', true)
                ->whereHas('membro', fn ($q) => $q->where('igreja_id', $igrejaId))
                ->orderByDesc('id')
                ->get(),
            'congregacoes' => CongregacoesCongregacao::query()
                ->where('instituicao_id', $igrejaId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(['id', 'nome']),
        ];
    }

    private function resolveCongregacaoId(?string $congregacaoInput): ?int
    {
        if ($congregacaoInput === null || $congregacaoInput === '' || $congregacaoInput === 'sede') {
            return null;
        }

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $congregacaoId = (int) $congregacaoInput;

        $exists = CongregacoesCongregacao::query()
            ->where('id', $congregacaoId)
            ->where('instituicao_id', $igrejaId)
            ->where('ativo', true)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'congregacao_id' => 'Congregação inválida para esta igreja.',
            ]);
        }

        return $congregacaoId;
    }

    private function syncAlunos(EbdTurma $turma, array $alunoIds): void
    {
        $alunoIds = collect($alunoIds)->map(fn ($id) => (int) $id)->unique()->values()->all();

        $currentActive = EbdTurmaAluno::where('turma_id', $turma->id)
            ->where('ativo', true)
            ->get()
            ->keyBy('aluno_id');

        $now = Carbon::now()->toDateString();

        foreach ($alunoIds as $alunoId) {
            if (! isset($currentActive[$alunoId])) {
                EbdTurmaAluno::create([
                    'turma_id' => $turma->id,
                    'aluno_id' => $alunoId,
                    'data_entrada' => $now,
                    'ativo' => true,
                ]);
            }
        }

        foreach ($currentActive as $alunoId => $vinculo) {
            if (! in_array((int) $alunoId, $alunoIds, true)) {
                $vinculo->update([
                    'ativo' => false,
                    'data_saida' => $now,
                ]);
            }
        }
    }

    private function validateProfessorAndAlunos(?int $professorId, array $alunoIds): void
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        if ($professorId !== null) {
            $professorValido = EbdProfessor::where('id', $professorId)
                ->where('ativo', true)
                ->whereHas('membro', fn ($q) => $q->where('igreja_id', $igrejaId))
                ->exists();

            if (! $professorValido) {
                throw ValidationException::withMessages([
                    'professor_id' => 'Professor inválido ou inativo para esta igreja.',
                ]);
            }
        }

        if (empty($alunoIds)) {
            return;
        }

        $qtd = EbdAluno::whereIn('id', $alunoIds)
            ->where('ativo', true)
            ->whereHas('membro', fn ($query) => $query->where('igreja_id', $igrejaId))
            ->count();

        if ($qtd !== count(array_unique($alunoIds))) {
            throw ValidationException::withMessages([
                'alunos' => 'Um ou mais alunos estão inválidos ou inativos.',
            ]);
        }
    }

    private function validateClasseByIgreja(int $classeId): void
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $classeValida = EbdClasse::where('id', $classeId)
            ->where('ativo', true)
            ->where('igreja_id', $igrejaId)
            ->exists();

        if (! $classeValida) {
            throw ValidationException::withMessages([
                'classe_id' => 'Classe inválida ou inativa para esta igreja.',
            ]);
        }
    }

    private function authorizeByIgreja(EbdTurma $turma): void
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        if ((int) $turma->classe?->igreja_id !== (int) $igrejaId) {
            abort(403);
        }
    }
}
