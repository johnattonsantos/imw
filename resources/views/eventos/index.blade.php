@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Eventos</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-3">
                <div class="row align-items-center">
                    <div class="col-lg-2 mb-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Pesquisar evento, local ou descrição">
                    </div>
                    <div class="col-lg-3 mb-2">
                        <select name="instituicao_id" class="form-control form-control-sm">
                            <option value="">Todas as igrejas/congregações</option>
                            @foreach ($instituicoesEvento->groupBy('grupo') as $grupo => $instituicoesGrupo)
                                <optgroup label="{{ $grupo }}">
                                    @foreach ($instituicoesGrupo as $instituicaoEvento)
                                        <option value="{{ $instituicaoEvento->id }}" {{ (string) request('instituicao_id') === (string) $instituicaoEvento->id ? 'selected' : '' }}>
                                            {{ $instituicaoEvento->label }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 mb-2">
                        <select name="evento_proposito_id" class="form-control form-control-sm">
                            <option value="">Todos os propósitos</option>
                            @foreach ($propositos as $proposito)
                                <option value="{{ $proposito->id }}" {{ (string) request('evento_proposito_id') === (string) $proposito->id ? 'selected' : '' }}>{{ $proposito->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 mb-2">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Todos os status</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 mb-2">
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="form-control form-control-sm" title="A partir de">
                    </div>
                    <div class="col-lg-1 mb-2">
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="form-control form-control-sm" title="Até">
                    </div>
                    <div class="col-lg-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="mb-3">
                @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-novo'))
                    <a href="{{ route('eventos.create') }}" class="btn btn-primary btn-sm">Novo</a>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            @if ($escopoEvento === 'regiao')
                                <th>Distrito</th>
                            @endif
                            @if (in_array($escopoEvento, ['regiao', 'distrito'], true))
                                <th>Igreja</th>
                            @endif
                            <th>Local</th>
                            <th>Propósito</th>
                            <th>Agenda</th>
                            <th>Líder</th>
                            <th>Status</th>
                            <th style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($eventos as $evento)
                            @php
                                $agenda = optional($evento->data_inicio)->format('d/m/Y');
                                if ($evento->hora_inicio) {
                                    $agenda .= ' ' . substr((string) $evento->hora_inicio, 0, 5);
                                }
                                if ($evento->data_fim) {
                                    $agenda .= ' até ' . optional($evento->data_fim)->format('d/m/Y');
                                    if ($evento->hora_fim) {
                                        $agenda .= ' ' . substr((string) $evento->hora_fim, 0, 5);
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $evento->titulo }}</td>
                                @if ($escopoEvento === 'regiao')
                                    <td>{{ $evento->evento_distrito_nome }}</td>
                                @endif
                                @if (in_array($escopoEvento, ['regiao', 'distrito'], true))
                                    <td>{{ $evento->evento_igreja_nome }}</td>
                                @endif
                                <td>{{ $evento->evento_local_nome }}</td>
                                <td>{{ optional($evento->proposito)->nome ?: '-' }}</td>
                                <td>{{ $agenda }}</td>
                                <td>{{ optional($evento->lider)->nome ?: '-' }}</td>
                                <td>{{ $statusOptions[$evento->status] ?? $evento->status }}</td>
                                <td class="table-action">
                                    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-sm btn-info btn-rounded bs-tooltip btn-evento-detalhes" title="Detalhes" aria-label="Detalhes" data-url="{{ route('eventos.show', $evento) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-eye">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-editar'))
                                        <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-sm btn-dark btn-rounded bs-tooltip" title="Editar" aria-label="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-edit-2">
                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-excluir'))
                                        <form method="POST" action="{{ route('eventos.destroy', $evento) }}" class="d-inline" onsubmit="return confirm('Deseja excluir este evento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-rounded bs-tooltip" title="Excluir" aria-label="Excluir">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-trash-2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6l-1 14H6L5 6"></path>
                                                    <path d="M10 11v6"></path>
                                                    <path d="M14 11v6"></path>
                                                    <path d="M9 6V4h6v2"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 7 + ($escopoEvento === 'regiao' ? 1 : 0) + (in_array($escopoEvento, ['regiao', 'distrito'], true) ? 1 : 0) }}" class="text-center">Nenhum evento encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $eventos->links('vendor.pagination.index') }}
        </div>
    </div>
</div>

<div class="modal fade" id="eventoDetalhesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body" style="min-height: 180px;">Carregando...</div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = $('#eventoDetalhesModal');
        const modalContent = modal.find('.modal-content');
        const loadingHtml = '<div class="modal-body" style="min-height: 180px;">Carregando...</div>';

        $(document).on('click', '.btn-evento-detalhes', function (event) {
            event.preventDefault();

            const url = $(this).data('url') || $(this).attr('href');
            modalContent.html(loadingHtml);
            modal.modal('show');

            $.ajax({
                url: url,
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (html) {
                    modalContent.html(html);
                },
                error: function () {
                    modalContent.html(
                        '<div class="modal-header">' +
                            '<h5 class="modal-title">Detalhes do Evento</h5>' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '</div>' +
                        '<div class="modal-body"><div class="alert alert-danger mb-0">Não foi possível carregar os detalhes do evento.</div></div>' +
                        '<div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button></div>'
                    );
                }
            });
        });
    });
</script>
@endsection
