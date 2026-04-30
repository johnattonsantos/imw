@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar Diário</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.diarios.update', $diario->id) }}" id="formDiario">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Turma</label>
                            <select name="turma_id" id="turma_id" class="form-control" required>
                                @foreach ($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ (int)$turma->id === (int)$diario->turma_id ? 'selected' : '' }}>{{ $turma->nome }} - {{ $turma->ano }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Data da aula</label>
                            <input type="date" name="data_aula" class="form-control" value="{{ optional($diario->data_aula)->format('Y-m-d') }}" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Hora início (HH:MM)</label>
                            <input type="text" name="hora_inicio" class="form-control" value="{{ old('hora_inicio', $diario->hora_inicio ? substr($diario->hora_inicio, 0, 5) : '') }}" placeholder="08:30" inputmode="numeric" pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$" title="Use formato 24h: HH:MM">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Hora fim (HH:MM)</label>
                            <input type="text" name="hora_fim" class="form-control" value="{{ old('hora_fim', $diario->hora_fim ? substr($diario->hora_fim, 0, 5) : '') }}" placeholder="09:45" inputmode="numeric" pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$" title="Use formato 24h: HH:MM">
                        </div>
                    </div>
                    <div class="form-group">
                        <small class="form-text text-muted">Formato brasileiro 24h: <strong>HH:MM</strong> (HH = hora, MM = minuto). Exemplo: 08:30 ou 19:45.</small>
                    </div>

                    <div class="form-group">
                        <label>Turno</label>
                        <select name="periodo_aula" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="manha" {{ old('periodo_aula', $diario->periodo_aula) === 'manha' ? 'selected' : '' }}>Manhã</option>
                            <option value="noite" {{ old('periodo_aula', $diario->periodo_aula) === 'noite' ? 'selected' : '' }}>Noite</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tema da aula</label>
                        <input type="text" name="tema_aula" class="form-control" value="{{ $diario->tema_aula }}" required>
                    </div>
                    <div class="form-group">
                        <label>Conteúdo</label>
                        <textarea name="conteudo" class="form-control" rows="4" required>{{ $diario->conteudo }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="observacoes" class="form-control" rows="3">{{ $diario->observacoes }}</textarea>
                    </div>

                    <h6>Presenças</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabelaPresencas">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Presente</th>
                                    <th>Justificativa</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('ebd.diarios.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
<script>
(function() {
    const turmaSelect = document.getElementById('turma_id');
    const tbody = document.querySelector('#tabelaPresencas tbody');
    const base = @json(route('ebd.diarios.turma-alunos', ['turma' => '__ID__']));
    const existing = @json($presencasExistentes);

    const render = (rows) => {
        tbody.innerHTML = '';
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center">Sem alunos ativos na turma.</td></tr>';
            return;
        }

        rows.forEach((row, index) => {
            const info = existing[row.aluno_id] || {};
            const checked = info.presente !== false;
            const justificativa = info.justificativa || '';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    ${row.nome}
                    <input type="hidden" name="presencas[${index}][aluno_id]" value="${row.aluno_id}">
                </td>
                <td>
                    <input type="checkbox" name="presencas[${index}][presente]" value="1" ${checked ? 'checked' : ''}>
                </td>
                <td>
                    <input type="text" class="form-control" name="presencas[${index}][justificativa]" value="${justificativa}">
                </td>
            `;
            tbody.appendChild(tr);
        });
    };

    const load = async () => {
        const turmaId = turmaSelect.value;
        if (!turmaId) {
            render([]);
            return;
        }

        const url = base.replace('__ID__', turmaId);
        const response = await fetch(url);
        const payload = await response.json();
        render(payload.data || []);
    };

    turmaSelect.addEventListener('change', load);
    load();
})();
</script>
@endsection
