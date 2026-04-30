<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdDiarioRequest;
use App\Http\Requests\UpdateEbdDiarioRequest;
use App\Models\EbdDiario;
use App\Models\EbdDiarioPresenca;
use App\Models\EbdTurma;
use App\Traits\Identifiable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EbdDiarioController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $diarios = EbdDiario::with(['turma.professor.membro'])
            ->whereHas('turma.professor.membro', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('data_aula')
            ->paginate(20);

        return view('ebd.diarios.index', compact('diarios'));
    }

    public function create()
    {
        return view('ebd.diarios.create', [
            'turmas' => $this->turmasDaIgreja(),
            'presencasExistentes' => [],
        ]);
    }

    public function store(StoreEbdDiarioRequest $request)
    {
        $data = $request->validated();

        $turma = $this->validateTurmaAndGet($data['turma_id']);
        $this->validatePresencasPertencemTurma($turma, $data['presencas']);

        DB::beginTransaction();
        try {
            $diario = EbdDiario::create([
                'turma_id' => $data['turma_id'],
                'data_aula' => $data['data_aula'],
                'hora_inicio' => $data['hora_inicio'] ?? null,
                'hora_fim' => $data['hora_fim'] ?? null,
                'periodo_aula' => $data['periodo_aula'],
                'tema_aula' => $data['tema_aula'],
                'conteudo' => $data['conteudo'],
                'observacoes' => $data['observacoes'] ?? null,
            ]);

            $this->savePresencas($diario, $data['presencas']);

            DB::commit();

            return redirect()->route('ebd.diarios.index')->with('success', 'Diário salvo com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function edit(EbdDiario $diario)
    {
        $this->authorizeByIgreja($diario);

        $diario->load('presencas');

        $presencasExistentes = $diario->presencas
            ->keyBy('aluno_id')
            ->map(fn ($item) => [
                'presente' => (bool) $item->presente,
                'justificativa' => $item->justificativa,
            ])
            ->toArray();

        return view('ebd.diarios.edit', [
            'diario' => $diario,
            'turmas' => $this->turmasDaIgreja(),
            'presencasExistentes' => $presencasExistentes,
        ]);
    }

    public function update(UpdateEbdDiarioRequest $request, EbdDiario $diario)
    {
        $this->authorizeByIgreja($diario);

        $data = $request->validated();

        $turma = $this->validateTurmaAndGet($data['turma_id']);
        $this->validatePresencasPertencemTurma($turma, $data['presencas']);

        DB::beginTransaction();
        try {
            $diario->update([
                'turma_id' => $data['turma_id'],
                'data_aula' => $data['data_aula'],
                'hora_inicio' => $data['hora_inicio'] ?? null,
                'hora_fim' => $data['hora_fim'] ?? null,
                'periodo_aula' => $data['periodo_aula'],
                'tema_aula' => $data['tema_aula'],
                'conteudo' => $data['conteudo'],
                'observacoes' => $data['observacoes'] ?? null,
            ]);

            EbdDiarioPresenca::where('diario_id', $diario->id)->delete();
            $this->savePresencas($diario, $data['presencas']);

            DB::commit();

            return redirect()->route('ebd.diarios.index')->with('success', 'Diário atualizado com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(EbdDiario $diario)
    {
        $this->authorizeByIgreja($diario);

        $diario->delete();

        return redirect()->route('ebd.diarios.index')->with('success', 'Diário removido com sucesso.');
    }

    public function turmaAlunos(EbdTurma $turma): JsonResponse
    {
        $this->authorizeTurmaByIgreja($turma);

        $alunos = $turma->alunosVinculos()
            ->with('aluno.membro')
            ->where('ativo', true)
            ->get()
            ->map(function ($vinculo) {
                return [
                    'aluno_id' => $vinculo->aluno_id,
                    'nome' => $vinculo->aluno?->membro?->nome,
                ];
            })
            ->values();

        return response()->json(['data' => $alunos]);
    }

    private function turmasDaIgreja()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        return EbdTurma::with(['classe', 'professor.membro'])
            ->where('ativo', true)
            ->whereHas('professor.membro', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get();
    }

    private function validateTurmaAndGet(int $turmaId): EbdTurma
    {
        $turma = EbdTurma::findOrFail($turmaId);
        $this->authorizeTurmaByIgreja($turma);

        if (! $turma->ativo) {
            throw ValidationException::withMessages([
                'turma_id' => 'A turma selecionada está inativa.',
            ]);
        }

        return $turma;
    }

    private function validatePresencasPertencemTurma(EbdTurma $turma, array $presencas): void
    {
        $alunoIds = collect($presencas)->pluck('aluno_id')->map(fn ($v) => (int) $v)->unique()->values();

        $count = $turma->alunosVinculos()
            ->where('ativo', true)
            ->whereIn('aluno_id', $alunoIds)
            ->count();

        if ($count !== $alunoIds->count()) {
            throw ValidationException::withMessages([
                'presencas' => 'Há alunos inválidos para a turma selecionada.',
            ]);
        }
    }

    private function savePresencas(EbdDiario $diario, array $presencas): void
    {
        foreach ($presencas as $presenca) {
            EbdDiarioPresenca::create([
                'diario_id' => $diario->id,
                'aluno_id' => $presenca['aluno_id'],
                'presente' => (bool) ($presenca['presente'] ?? false),
                'justificativa' => $presenca['justificativa'] ?? null,
            ]);
        }
    }

    private function authorizeByIgreja(EbdDiario $diario): void
    {
        $this->authorizeTurmaByIgreja($diario->turma);
    }

    private function authorizeTurmaByIgreja(EbdTurma $turma): void
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        if ((int) $turma->professor?->membro?->igreja_id !== (int) $igrejaId) {
            abort(403);
        }
    }
}
