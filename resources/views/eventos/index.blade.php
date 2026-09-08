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
                    <h4>{{ __('Eventos') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="mb-3">
                @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-novo'))
                    <a href="{{ route('eventos.create') }}" class="btn btn-primary btn-sm">{{ __('Novo') }}</a>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Evento') }}</th>
                            @if ($escopoEvento === 'regiao')
                                <th>{{ __('Distrito') }}</th>
                            @endif
                            @if (in_array($escopoEvento, ['regiao', 'distrito'], true))
                                <th>{{ __('Igreja') }}</th>
                            @endif
                            <th>{{ __('Local') }}</th>
                            <th>{{ __('Tipo') }}</th>
                            <th>{{ __('Agenda') }}</th>
                            <th>{{ __('Líder') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th style="width: 190px;">{{ __('Ações') }}</th>
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
                                    $agenda .= ' ' . __('até') . ' ' . optional($evento->data_fim)->format('d/m/Y');
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
                                    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-sm btn-info btn-rounded bs-tooltip btn-evento-detalhes" title="{{ __('Detalhes') }}" aria-label="{{ __('Detalhes') }}" data-url="{{ route('eventos.show', $evento) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-eye">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success btn-rounded bs-tooltip btn-evento-inscricao" title="{{ __('Inscrever Pessoa') }}" aria-label="{{ __('Inscrever Pessoa') }}" data-action="{{ route('eventos.inscricoes.store', $evento) }}" data-evento="{{ $evento->titulo }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-users">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </button>
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-editar'))
                                        <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-sm btn-dark btn-rounded bs-tooltip" title="{{ __('Editar') }}" aria-label="{{ __('Editar') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-edit-2">
                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-excluir'))
                                        <form method="POST" action="{{ route('eventos.destroy', $evento) }}" class="d-inline" onsubmit="return confirm(@js(__('Deseja excluir este evento?')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-rounded bs-tooltip" title="{{ __('Excluir') }}" aria-label="{{ __('Excluir') }}">
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
                                <td colspan="{{ 7 + ($escopoEvento === 'regiao' ? 1 : 0) + (in_array($escopoEvento, ['regiao', 'distrito'], true) ? 1 : 0) }}" class="text-center">{{ __('Nenhum evento encontrado.') }}</td>
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
            <div class="modal-body" style="min-height: 180px;">{{ __('Carregando...') }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventoInscricaoModal" tabindex="-1" role="dialog" aria-labelledby="eventoInscricaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="evento-inscricao-form" method="POST" action="#">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="eventoInscricaoModalLabel">{{ __('Inscrever no Evento') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Fechar') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>{{ __('Evento') }}:</strong>
                        <span id="evento-inscricao-titulo">-</span>
                    </p>
                    <div id="evento-inscricao-feedback" class="alert d-none"></div>
                    <div class="form-group">
                        <label for="evento_inscricao_cpf">{{ __('* CPF do membro/clérigo') }}</label>
                        <input type="text" name="cpf" id="evento_inscricao_cpf" class="form-control" maxlength="14" placeholder="000.000.000-00" autocomplete="off" required>
                        <small class="form-text text-muted">{{ __('Informe o CPF de um membro ou clérigo cadastrado no sistema.') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Confirmar') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = $('#eventoDetalhesModal');
        const modalContent = modal.find('.modal-content');
        const loadingHtml = '<div class="modal-body" style="min-height: 180px;">' + window.__('Carregando...') + '</div>';

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
                            '<h5 class="modal-title">' + window.__('Detalhes do Evento') + '</h5>' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="' + window.__('Fechar') + '"><span aria-hidden="true">&times;</span></button>' +
                        '</div>' +
                        '<div class="modal-body"><div class="alert alert-danger mb-0">' + window.__('Não foi possível carregar os detalhes do evento.') + '</div></div>' +
                        '<div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">' + window.__('Fechar') + '</button></div>'
                    );
                }
            });
        });

        const inscricaoModal = $('#eventoInscricaoModal');
        const inscricaoForm = document.getElementById('evento-inscricao-form');
        const inscricaoCpf = document.getElementById('evento_inscricao_cpf');
        const inscricaoFeedback = document.getElementById('evento-inscricao-feedback');

        function showInscricaoFeedback(type, message) {
            if (!inscricaoFeedback) {
                return;
            }

            inscricaoFeedback.className = 'alert alert-' + type;
            inscricaoFeedback.innerHTML = message;
        }

        function resetInscricaoFeedback() {
            if (!inscricaoFeedback) {
                return;
            }

            inscricaoFeedback.className = 'alert d-none';
            inscricaoFeedback.innerHTML = '';
        }

        if (inscricaoCpf) {
            inscricaoCpf.addEventListener('input', function (event) {
                let value = event.target.value.replace(/\D/g, '').slice(0, 11);
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                event.target.value = value;
            });
        }

        $(document).on('click', '.btn-evento-inscricao', function () {
            const button = $(this);
            const titulo = button.data('evento') || '-';
            const action = button.data('action');

            resetInscricaoFeedback();
            $('#evento-inscricao-titulo').text(titulo);

            if (inscricaoForm) {
                inscricaoForm.action = action;
                inscricaoForm.reset();
            }

            inscricaoModal.modal('show');
        });

        if (inscricaoForm) {
            inscricaoForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const form = event.currentTarget;
                const submitButton = form.querySelector('button[type="submit"]');

                resetInscricaoFeedback();
                if (submitButton) {
                    submitButton.disabled = true;
                }

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async function (response) {
                        const data = await response.json().catch(function () {
                            return {};
                        });

                        if (!response.ok) {
                            const errors = data.errors
                                ? Object.values(data.errors).flat().join('<br>')
                                : (data.message || '{{ __('Não foi possível realizar a inscrição.') }}');
                            throw new Error(errors);
                        }

                        return data;
                    })
                    .then(function (data) {
                        const inscrito = data.inscrito || {};
                        const message = data.message || '{{ __('Inscrição realizada com sucesso.') }}';
                        const detalhes = inscrito.nome
                            ? '<br><strong>' + inscrito.nome + '</strong> - ' + (inscrito.cpf || '')
                            : '';

                        showInscricaoFeedback('success', message + detalhes);
                        form.reset();
                    })
                    .catch(function (error) {
                        showInscricaoFeedback('danger', error.message);
                    })
                    .finally(function () {
                        if (submitButton) {
                            submitButton.disabled = false;
                        }
                    });
            });
        }
    });
</script>
@endsection
