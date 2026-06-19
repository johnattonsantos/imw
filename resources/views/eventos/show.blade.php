@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Detalhes', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Detalhes do Evento</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
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
            <div class="mb-3">{!! nl2br(e($evento->descricao ?: '-')) !!}</div>
            <p><strong>Observações:</strong></p>
            <div class="mb-3">{!! nl2br(e($evento->observacoes ?: '-')) !!}</div>

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

            @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-editar'))
                <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-dark">Editar</a>
            @endif
            <a href="{{ route('eventos.index') }}" class="btn btn-light">Voltar</a>
        </div>
    </div>
</div>
@endsection
