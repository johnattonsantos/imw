<p><strong>{{ __('Nome') }}:</strong> {{ $evento->titulo }}</p>
<p><strong>{{ __('Propósito') }}:</strong> {{ optional($evento->proposito)->nome ?: '-' }}</p>
<p><strong>{{ __('Status') }}:</strong> {{ $statusOptions[$evento->status] ?? $evento->status }}</p>
@if (($evento->evento_distrito_nome ?? '-') !== '-')
    <p><strong>{{ __('Distrito') }}:</strong> {{ $evento->evento_distrito_nome }}</p>
@endif
@if (($evento->evento_igreja_nome ?? '-') !== '-')
    <p><strong>{{ __('Igreja') }}:</strong> {{ $evento->evento_igreja_nome }}</p>
@endif
<p><strong>{{ __('Sede/Congregação') }}:</strong> {{ $evento->evento_local_nome ?? '-' }}</p>
<p><strong>{{ __('Agenda') }}:</strong>
    {{ optional($evento->data_inicio)->format('d/m/Y') }}
    {{ $evento->hora_inicio ? substr((string) $evento->hora_inicio, 0, 5) : '' }}
    @if ($evento->data_fim)
        {{ __('até') }} {{ optional($evento->data_fim)->format('d/m/Y') }} {{ $evento->hora_fim ? substr((string) $evento->hora_fim, 0, 5) : '' }}
    @endif
</p>
<p><strong>{{ __('Local informado') }}:</strong> {{ $evento->local ?: '-' }}</p>
<p><strong>{{ __('Descrição / Agenda') }}:</strong></p>
<div class="mb-3">{!! $evento->descricao ?: '-' !!}</div>
<p><strong>{{ __('Observações') }}:</strong></p>
<div class="mb-3">{!! $evento->observacoes ?: '-' !!}</div>

<h5>{{ __('Equipe de Coordenação') }}</h5>
<div class="table-responsive mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('Nome') }}</th>
                <th>{{ __('Função') }}</th>
                <th>{{ __('Contato') }}</th>
                <th>{{ __('Líder') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($evento->equipe as $membro)
                <tr>
                    <td>{{ $membro->nome }}</td>
                    <td>{{ $membro->funcao ?: '-' }}</td>
                    <td>{{ $membro->contato ?: '-' }}</td>
                    <td>{{ $membro->lider ? __('Sim') : __('Não') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">{{ __('Nenhuma pessoa cadastrada na equipe.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
