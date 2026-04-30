@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Nova Turma</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.turmas.store') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Classe</label>
                            <select name="classe_id" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach ($classes as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Professor</label>
                            <select name="professor_id" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach ($professores as $prof)
                                    <option value="{{ $prof->id }}">{{ $prof->membro->nome ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Ano</label>
                            <input type="number" name="ano" class="form-control" value="{{ date('Y') }}" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Semestre</label>
                            <select name="semestre" class="form-control">
                                <option value="">-</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alunos (somente ativos)</label>
                        @php($alunosOld = old('alunos', []))
                        <div class="mb-2">
                            <label class="mb-0">
                                <input type="checkbox" id="marcarTodosAlunos">
                                Marcar todos
                            </label>
                        </div>
                        <div class="border rounded p-2" style="max-height: 280px; overflow-y: auto;">
                            @forelse ($alunos as $aluno)
                                @php($checked = in_array((string) $aluno->id, array_map('strval', $alunosOld), true))
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
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>

                    <button class="btn btn-success">Salvar</button>
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
