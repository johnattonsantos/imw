@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Tipo Arquivo Comunicacao', 'url' => '/tipo-arquivo-comunicacao', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('extras-scripts')
<script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
<script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Tipo de Arquivo da Comunicacao</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-3">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                            placeholder="Pesquisar por extensao...">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="mb-3 d-flex" style="gap: 8px;">
                <button type="button" id="btn-open-create" class="btn btn-primary btn-sm">Novo tipo</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Extensao</th>
                            <th>Criado em</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos as $tipo)
                            <tr id="row-tipo-{{ $tipo->id }}">
                                <td>.{{ strtolower((string) $tipo->extensao) }}</td>
                                <td>{{ optional($tipo->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="table-action">
                                    <button type="button" class="btn btn-sm btn-dark btn-rounded btn-edit bs-tooltip" data-id="{{ $tipo->id }}" title="Editar" aria-label="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger btn-rounded btn-delete bs-tooltip" data-id="{{ $tipo->id }}" title="Excluir" aria-label="Excluir">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6l-1 14H6L5 6"></path>
                                            <path d="M10 11v6"></path>
                                            <path d="M14 11v6"></path>
                                            <path d="M9 6V4h6v2"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Nenhum registro encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $tipos->links('vendor.pagination.index') }}
        </div>
    </div>
</div>

<div class="modal fade" id="modalTipoForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTipoFormTitle">Tipo de Arquivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTipoAjax" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="tipo-id" name="id" value="">
                    <div class="form-group">
                        <label for="tipo-extensao">* Extensao</label>
                        <input type="text" id="tipo-extensao" name="extensao" class="form-control" maxlength="10" required placeholder="Ex: pdf">
                        <small class="text-muted d-block mt-2">Informe somente a extensao, sem ponto.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const storeUrl = "{{ route('tipo-arquivo-comunicacao.store') }}";
        const showUrlTemplate = "{{ route('tipo-arquivo-comunicacao.show', ['tipoArquivoComunicacao' => '__ID__']) }}";
        const updateUrlTemplate = "{{ route('tipo-arquivo-comunicacao.update', ['tipoArquivoComunicacao' => '__ID__']) }}";
        const deleteUrlTemplate = "{{ route('tipo-arquivo-comunicacao.destroy', ['tipoArquivoComunicacao' => '__ID__']) }}";

        let currentMode = 'create';

        function buildUrl(template, id) {
            return template.replace('__ID__', id);
        }

        function showValidationErrors(xhr) {
            if (xhr.status !== 422 || !xhr.responseJSON || !xhr.responseJSON.errors) {
                const fallbackMessage = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Falha ao processar a requisicao.';
                swal('Erro', fallbackMessage, 'error');
                return;
            }

            const messages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            swal({
                title: 'Validacao',
                html: messages,
                type: 'error'
            });
        }

        function resetForm() {
            $('#formTipoAjax')[0].reset();
            $('#tipo-id').val('');
        }

        function loadTipo(id, callback) {
            $.ajax({
                url: buildUrl(showUrlTemplate, id),
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                success: function(resp) {
                    callback(resp.data);
                },
                error: function(xhr) {
                    const message = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Nao foi possivel carregar o registro.';
                    swal('Erro', message, 'error');
                }
            });
        }

        $('#btn-open-create').on('click', function() {
            currentMode = 'create';
            resetForm();
            $('#modalTipoFormTitle').text('Novo tipo');
            $('#modalTipoForm').modal('show');
        });

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            currentMode = 'edit';
            resetForm();

            loadTipo(id, function(data) {
                $('#tipo-id').val(data.id);
                $('#tipo-extensao').val(data.extensao);
                $('#modalTipoFormTitle').text('Editar tipo');
                $('#modalTipoForm').modal('show');
            });
        });

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const url = buildUrl(deleteUrlTemplate, id);

            swal({
                title: 'Confirmacao',
                text: 'Deseja realmente excluir este tipo?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Excluir',
                cancelButtonText: 'Cancelar',
                padding: '2em'
            }).then(function(result) {
                if (!result.value) {
                    return;
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(resp) {
                        $('#row-tipo-' + id).remove();
                        swal('Sucesso', resp.message || 'Tipo excluido com sucesso.', 'success');
                    },
                    error: function(xhr) {
                        showValidationErrors(xhr);
                    }
                });
            });
        });

        $('#formTipoAjax').on('submit', function(e) {
            e.preventDefault();

            const extensao = ($('#tipo-extensao').val() || '').trim().toLowerCase().replace(/^\.+/, '');
            if (!extensao) {
                swal('Validacao', 'O campo Extensao e obrigatorio.', 'error');
                return;
            }

            const id = $('#tipo-id').val();
            const payload = { extensao: extensao };
            let url = storeUrl;

            if (currentMode === 'edit' && id) {
                url = buildUrl(updateUrlTemplate, id);
            }

            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                data: payload,
                success: function(resp) {
                    $('#modalTipoForm').modal('hide');
                    swal('Sucesso', resp.message || 'Operacao realizada com sucesso.', 'success');
                    window.location.reload();
                },
                error: function(xhr) {
                    showValidationErrors(xhr);
                }
            });
        });
    })();
</script>
@endsection
