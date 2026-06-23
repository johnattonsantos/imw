<p><strong>Nome:</strong> {{ $evento->titulo }}</p>
<p><strong>Propósito:</strong> {{ optional($evento->proposito)->nome ?: '-' }}</p>
<p><strong>Status:</strong> {{ $statusOptions[$evento->status] ?? $evento->status }}</p>
<p><strong>Agenda:</strong>
    {{ optional($evento->data_inicio)->format('d/m/Y') }}
    {{ $evento->hora_inicio ? substr((string) $evento->hora_inicio, 0, 5) : '' }}
    @if ($evento->data_fim)
        até {{ optional($evento->data_fim)->format('d/m/Y') }} {{ $evento->hora_fim ? substr((string) $evento->hora_fim, 0, 5) : '' }}
    @endif
</p>
<p><strong>Local:</strong> {{ $evento->local ?: '-' }}</p>
<p><strong>Descrição / Agenda:</strong></p>
<div class="mb-3">{!! $evento->descricao ?: '-' !!}</div>
<p><strong>Observações:</strong></p>
<div class="mb-3">{!! $evento->observacoes ?: '-' !!}</div>

<h5>Equipe de Coordenação</h5>
<div class="table-responsive mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Função</th>
                <th>Contato</th>
                <th>Líder</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($evento->equipe as $membro)
                <tr>
                    <td>{{ $membro->nome }}</td>
                    <td>{{ $membro->funcao ?: '-' }}</td>
                    <td>{{ $membro->contato ?: '-' }}</td>
                    <td>{{ $membro->lider ? 'Sim' : 'Não' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhuma pessoa cadastrada na equipe.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
