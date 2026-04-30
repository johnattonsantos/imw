@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar Turma</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.turmas.update', $turma->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Classe</label>
                            <select name="classe_id" class="form-control" required>
                                @foreach ($classes as $classe)
                                    <option value="{{ $classe->id }}" {{ (int)$classe->id === (int)$turma->classe_id ? 'selected' : '' }}>{{ $classe->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Professor</label>
                            <select name="professor_id" class="form-control" required>
                                @foreach ($professores as $prof)
                                    <option value="{{ $prof->id }}" {{ (int)$prof->id === (int)$turma->professor_id ? 'selected' : '' }}>{{ $prof->membro->nome ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control" value="{{ $turma->nome }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Ano</label>
                            <input type="number" name="ano" class="form-control" value="{{ $turma->ano }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Semestre</label>
                            <select name="semestre" class="form-control">
                                <option value="" {{ !$turma->semestre ? 'selected' : '' }}>-</option>
                                <option value="1" {{ (int)$turma->semestre === 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ (int)$turma->semestre === 2 ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alunos (somente ativos)</label>
                        @php($alunosSelecionados = old('alunos', $alunosAtivosVinculados ?? []))
                        @php($alunosSelecionadosStr = array_map('strval', $alunosSelecionados))
                        <div class="mb-2">
                            <label class="mb-0">
                                <input type="checkbox" id="marcarTodosAlunos">
                                Marcar todos
                            </label>
                        </div>
                        <div class="border rounded p-2" style="max-height: 280px; overflow-y: auto;">
                            @forelse ($alunos as $aluno)
                                @php($checked = in_array((string) $aluno->id, $alunosSelecionadosStr, true))
                                <div class="form-check">
                                    <input class="form-check-input aluno-checkbox" type="checkbox" name="alunos[]" value="{{ $aluno->id }}" id="aluno_{{ $aluno->id }}" {{ $checked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="aluno_{{ $aluno->id }}">
                                        {{ $aluno->membro->nome ?? '-' }}
                                    </label>
                                </div>
                            @empty
                                <div class="text-muted">Nenhum aluno ativo disponível.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control" required>
                            <option value="1" {{ $turma->ativo ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ !$turma->ativo ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('ebd.turmas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
<script>
(function () {
    const marcarTodos = document.getElementById('marcarTodosAlunos');
    const checkboxes = Array.from(document.querySelectorAll('.aluno-checkbox'));

    if (!marcarTodos || !checkboxes.length) {
        return;
    }

    const syncMarcarTodos = () => {
        marcarTodos.checked = checkboxes.every((checkbox) => checkbox.checked);
    };

    marcarTodos.addEventListener('change', () => {
        checkboxes.forEach((checkbox) => {
            checkbox.checked = marcarTodos.checked;
        });
    });

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', syncMarcarTodos);
    });

    syncMarcarTodos();
})();
</script>
@endsection
