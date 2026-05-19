<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEbdAlunoRequest;
use App\Http\Requests\UpdateEbdAlunoRequest;
use App\Models\EbdAluno;
use App\Models\EbdTurma;
use App\Models\EbdTurmaAluno;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EbdAlunoController extends Controller
{
    use Identifiable;

    public function index()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $alunos = EbdAluno::with(['membro.contato'])
            ->whereHas('membro', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('ativo')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('ebd.alunos.index', compact('alunos'));
    }

    public function create()
    {
        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $membros = MembresiaMembro::query()
            ->where('igreja_id', $igrejaId)
            ->where('status', MembresiaMembro::STATUS_ATIVO)
            ->whereIn('vinculo', [
                MembresiaMembro::VINCULO_MEMBRO,
                MembresiaMembro::VINCULO_CONGREGADO,
                MembresiaMembro::VINCULO_VISITANTE,
            ])
            ->orderBy('nome')
            ->get(['id', 'nome', 'vinculo']);

        $instituicao = Identifiable::fetchSessionIgrejaLocal()->nome;

        return view('ebd.alunos.create', compact('membros', 'instituicao'));
    }

    public function store(StoreEbdAlunoRequest $request)
    {
        $data = $request->validated();
        $this->validateNoDuplicate($data);

        $aluno = EbdAluno::create($data);

        return redirect()
            ->route('ebd.alunos.vinculos', $aluno->id)
            ->with('success', 'Aluno vinculado com sucesso. Agora escolha a EBD para inclusão.');
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

        DB::beginTransaction();
        try {
            DB::table('ebd_diario_presencas')
                ->where('aluno_id', $aluno->id)
                ->delete();

            DB::table('ebd_turma_alunos')
                ->where('aluno_id', $aluno->id)
                ->delete();

            $aluno->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('ebd.alunos.index')
                ->with('error', 'Não foi possível remover o aluno. Verifique os vínculos existentes e tente novamente.');
        }

        return redirect()->route('ebd.alunos.index')->with('success', 'Aluno removido com sucesso.');
    }

    public function vinculos(EbdAluno $aluno)
    {
        $this->authorizeByIgreja($aluno);

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;

        $turmas = EbdTurma::with(['classe', 'professor.membro'])
            ->where('ativo', true)
            ->whereHas('classe', fn ($q) => $q->where('igreja_id', $igrejaId)->where('ativo', true))
            ->orderByDesc('ano')
            ->orderBy('nome')
            ->get();

        $vinculosAtivos = EbdTurmaAluno::with(['turma.classe', 'turma.professor.membro'])
            ->where('aluno_id', $aluno->id)
            ->where('ativo', true)
            ->whereHas('turma.classe', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->orderByDesc('created_at')
            ->get();

        $instituicao = Identifiable::fetchSessionIgrejaLocal()->nome;

        return view('ebd.alunos.vinculos', compact('aluno', 'turmas', 'vinculosAtivos', 'instituicao'));
    }

    public function vincularTurma(Request $request, EbdAluno $aluno)
    {
        $this->authorizeByIgreja($aluno);

        $data = $request->validate([
            'turma_id' => ['required', 'integer', 'exists:ebd_turmas,id'],
        ]);

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $turma = EbdTurma::query()
            ->where('id', (int) $data['turma_id'])
            ->where('ativo', true)
            ->whereHas('classe', fn ($q) => $q->where('igreja_id', $igrejaId)->where('ativo', true))
            ->first();

        if (! $turma) {
            return redirect()->back()->with('error', 'EBD inválida para a igreja logada.');
        }

        $vinculoAtivo = EbdTurmaAluno::query()
            ->where('turma_id', $turma->id)
            ->where('aluno_id', $aluno->id)
            ->where('ativo', true)
            ->first();

        if ($vinculoAtivo) {
            return redirect()->back()->with('success', 'Esse aluno já está incluído nessa EBD.');
        }

        $vinculoInativo = EbdTurmaAluno::query()
            ->where('turma_id', $turma->id)
            ->where('aluno_id', $aluno->id)
            ->where('ativo', false)
            ->latest('id')
            ->first();

        if ($vinculoInativo) {
            $vinculoInativo->update([
                'ativo' => true,
                'data_saida' => null,
                'data_entrada' => Carbon::now()->toDateString(),
            ]);
        } else {
            EbdTurmaAluno::create([
                'turma_id' => $turma->id,
                'aluno_id' => $aluno->id,
                'data_entrada' => Carbon::now()->toDateString(),
                'ativo' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Aluno incluído na EBD com sucesso.');
    }

    public function removerTurma(EbdAluno $aluno, EbdTurma $turma)
    {
        $this->authorizeByIgreja($aluno);

        $igrejaId = Identifiable::fetchSessionIgrejaLocal()->id;
        $turmaValida = EbdTurma::query()
            ->where('id', $turma->id)
            ->whereHas('classe', fn ($q) => $q->where('igreja_id', $igrejaId))
            ->exists();

        if (! $turmaValida) {
            return redirect()->back()->with('error', 'EBD inválida para esta igreja.');
        }

        $vinculoAtivo = EbdTurmaAluno::query()
            ->where('turma_id', $turma->id)
            ->where('aluno_id', $aluno->id)
            ->where('ativo', true)
            ->first();

        if (! $vinculoAtivo) {
            return redirect()->back()->with('success', 'Esse vínculo já está inativo.');
        }

        $vinculoAtivo->update([
            'ativo' => false,
            'data_saida' => Carbon::now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Aluno removido da EBD com sucesso.');
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
