@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Comunicação', 'url' => '/comunicacao', 'active' => true],
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
<script src="{{ asset('gceu/tinymce/tinymce.min.js') }}?time={{ time() }}"></script>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Comunicação</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-3">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                            placeholder="Pesquisar por título ou comentário...">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="mb-3 d-flex" style="gap: 8px;">
                <button type="button" id="btn-open-create" class="btn btn-primary btn-sm">Novo</button>
                <a href="{{ route('comunicacao.export.xlsx', request()->query()) }}" class="btn btn-success btn-sm">Exportar XLSX</a>
                <a href="{{ route('comunicacao.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">Exportar PDF</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Título</th>
                            <th>Comentário</th>
                            <th>Arquivo</th>
                            <th style="width:150px;">Criado em</th>
                            <th style="width:240px;">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($comunicacoes as $comunicacao)
                            <tr id="row-comunicacao-{{ $comunicacao->id }}">
                                <td>{{ optional($comunicacao->categoria)->nome ?: '-' }}</td>
                                <td>{{ $comunicacao->titulo }}</td>
                                @php
                                    $comentarioTexto = html_entity_decode(strip_tags((string) $comunicacao->comentario), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    $comentarioTexto = preg_replace('/\s+/', ' ', trim($comentarioTexto));
                                @endphp
                                <td>{{ \Illuminate\Support\Str::limit($comentarioTexto, 80) }}</td>
                                <td>
                                    @if ($comunicacao->arquivo)
                                        @php
                                            $arquivoExt = strtolower((string) pathinfo($comunicacao->arquivo, PATHINFO_EXTENSION));
                                            $iconClass = 'fa-file';
                                            $colorClass = 'text-muted';
                                            if ($arquivoExt === 'pdf') {
                                                $iconClass = 'fa-file-pdf';
                                                $colorClass = 'text-danger';
                                            } elseif (in_array($arquivoExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                                                $iconClass = 'fa-file-image';
                                                $colorClass = 'text-info';
                                            } elseif (in_array($arquivoExt, ['doc', 'docx'], true)) {
                                                $iconClass = 'fa-file-word';
                                                $colorClass = 'text-primary';
                                            } elseif (in_array($arquivoExt, ['xls', 'xlsx'], true)) {
                                                $iconClass = 'fa-file-excel';
                                                $colorClass = 'text-success';
                                            } elseif (in_array($arquivoExt, ['zip', 'rar'], true)) {
                                                $iconClass = 'fa-file-archive';
                                                $colorClass = 'text-warning';
                                            }
                                        @endphp
                                        <a href="{{ route('comunicacao.visualizar', $comunicacao) }}" target="_blank" title="Ver arquivo">
                                            <i class="fas {{ $iconClass }} {{ $colorClass }} fa-lg"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ optional($comunicacao->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="table-action">
                                    <button type="button" class="btn btn-sm btn-info btn-rounded btn-show bs-tooltip" data-id="{{ $comunicacao->id }}" title="Detalhes" aria-label="Detalhes">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-dark btn-rounded btn-edit bs-tooltip" data-id="{{ $comunicacao->id }}" title="Editar" aria-label="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger btn-rounded btn-delete bs-tooltip" data-id="{{ $comunicacao->id }}" title="Excluir" aria-label="Excluir">
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
                                <td colspan="6" class="text-center">Nenhum registro encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $comunicacoes->links('vendor.pagination.index') }}
        </div>
    </div>
</div>

<div class="modal fade" id="modalComunicacaoForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalComunicacaoFormTitle">Comunicação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formComunicacaoAjax" enctype="multipart/form-data" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="comunicacao-id" name="id" value="">

                    <div class="form-group">
                        <label for="comunicacao-categoria">* Categoria</label>
                        <select id="comunicacao-categoria" name="categoria_comunicacao_id" class="form-control" required>
                            <option value="">Selecione</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comunicacao-titulo">* Título</label>
                        <input type="text" id="comunicacao-titulo" name="titulo" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="comunicacao-comentario">* Comentário</label>
                        <textarea id="comunicacao-comentario" name="comentario" class="form-control" rows="8"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="comunicacao-arquivo">Arquivo</label>
                        <input type="file" id="comunicacao-arquivo" name="arquivo" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.zip,.rar">
                        <small class="text-muted d-block mt-2">Formatos: PDF, imagem, Word, Excel, ZIP, RAR. Tamanho maximo: 10MB</small>
                        <small class="text-muted d-block mt-2" id="comunicacao-arquivo-atual"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-comunicacao">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalComunicacaoShow" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Comunicação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Categoria:</strong> <span id="show-categoria"></span></p>
                <p><strong>Título:</strong> <span id="show-titulo"></span></p>
                <p><strong>Comentário:</strong></p>
                <div id="show-comentario"></div>
                <p><strong>Arquivo:</strong> <span id="show-arquivo"></span></p>
                <p><strong>Criado em:</strong> <span id="show-created-at"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const storeUrl = "{{ route('comunicacao.store') }}";
        const showUrlTemplate = "{{ route('comunicacao.show', ['comunicacao' => '__ID__']) }}";
        const updateUrlTemplate = "{{ route('comunicacao.update.ajax', ['comunicacao' => '__ID__']) }}";
        const deleteUrlTemplate = "{{ route('comunicacao.destroy', ['comunicacao' => '__ID__']) }}";
        const visualizarUrlTemplate = "{{ route('comunicacao.visualizar', ['comunicacao' => '__ID__']) }}";

        let currentMode = 'create';

        function hasTinyMce() {
            return typeof window.tinymce !== 'undefined';
        }

        function buildUrl(template, id) {
            return template.replace('__ID__', id);
        }

        function initEditor() {
            if (!hasTinyMce()) {
                return;
            }

            if (window.tinymce.get('comunicacao-comentario')) {
                window.tinymce.get('comunicacao-comentario').remove();
            }

            window.tinymce.init({
                selector: '#comunicacao-comentario',
                height: 300,
                menubar: true,
                language: 'pt_BR',
                theme: 'modern',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code',
                    'responsivefilemanager',
                ],
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent forecolor | responsivefilemanager',
                relative_urls: false,
                remove_script_host: false,
                image_advtab: true,
                external_filemanager_path: '/gceu/tinymce/filemanager/',
                filemanager_title: 'Procurar imagem',
                external_plugins: {
                    filemanager: "{{ asset('gceu/tinymce/filemanager/plugin.min.js') }}"
                },
                content_css: ['//www.tinymce.com/css/codepen.min.css']
            });
        }

        function getEditor() {
            if (!hasTinyMce()) {
                return null;
            }

            return window.tinymce.get('comunicacao-comentario');
        }

        function setEditorContent(html) {
            const content = html || '';
            const editor = getEditor();

            if (editor) {
                editor.setContent(content);
                return;
            }

            $('#comunicacao-comentario').val(content);
        }

        function getEditorContent() {
            const editor = getEditor();
            if (editor) {
                return editor.getContent();
            }

            return $('#comunicacao-comentario').val();
        }

        function isRichTextEmpty(html) {
            const plain = (html || '')
                .replace(/<[^>]*>/g, '')
                .replace(/&nbsp;/gi, ' ')
                .trim();
            return plain.length === 0;
        }

        function getFileMetaByExtension(ext) {
            const normalized = (ext || '').toLowerCase();
            if (normalized === 'pdf') {
                return { iconClass: 'fa-file-pdf', colorClass: 'text-danger', title: 'PDF' };
            }
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(normalized)) {
                return { iconClass: 'fa-file-image', colorClass: 'text-info', title: 'Imagem' };
            }
            if (['doc', 'docx'].includes(normalized)) {
                return { iconClass: 'fa-file-word', colorClass: 'text-primary', title: 'Word' };
            }
            if (['xls', 'xlsx'].includes(normalized)) {
                return { iconClass: 'fa-file-excel', colorClass: 'text-success', title: 'Excel' };
            }
            if (['zip', 'rar'].includes(normalized)) {
                return { iconClass: 'fa-file-archive', colorClass: 'text-warning', title: 'Compactado' };
            }

            return { iconClass: 'fa-file', colorClass: 'text-muted', title: 'Arquivo' };
        }

        function buildFileIconLink(url, ext) {
            if (!url) {
                return '-';
            }

            const meta = getFileMetaByExtension(ext);
            return '<a href="' + url + '" target="_blank" title="Ver arquivo"><i class="fas ' + meta.iconClass + ' ' + meta.colorClass + ' fa-lg"></i></a>';
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
            $('#formComunicacaoAjax')[0].reset();
            $('#comunicacao-id').val('');
            $('#comunicacao-categoria').val('');
            $('#comunicacao-arquivo-atual').html('');
            setEditorContent('');
        }

        function loadComunicacao(id, callback) {
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
                        : 'Não foi possível carregar o registro.';
                    swal('Erro', message, 'error');
                }
            });
        }

        $('#btn-open-create').on('click', function() {
            currentMode = 'create';
            resetForm();
            $('#modalComunicacaoFormTitle').text('Nova Comunicação');
            $('#modalComunicacaoForm').modal('show');
        });

        $('#modalComunicacaoForm').on('shown.bs.modal', function() {
            if (!getEditor() && hasTinyMce()) {
                initEditor();
            }
        });

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            currentMode = 'edit';
            resetForm();

            loadComunicacao(id, function(data) {
                $('#comunicacao-id').val(data.id);
                $('#comunicacao-categoria').val(data.categoria_comunicacao_id || '');
                $('#comunicacao-titulo').val(data.titulo);
                setEditorContent(data.comentario_html || '');

                if (data.arquivo_visualizar_url) {
                    $('#comunicacao-arquivo-atual').html('Arquivo atual: ' + buildFileIconLink(data.arquivo_visualizar_url, data.arquivo_ext));
                }

                $('#modalComunicacaoFormTitle').text('Editar Comunicação');
                $('#modalComunicacaoForm').modal('show');
            });
        });

        $(document).on('click', '.btn-show', function() {
            const id = $(this).data('id');

            loadComunicacao(id, function(data) {
                $('#show-categoria').text(data.categoria_nome || '-');
                $('#show-titulo').text(data.titulo || '');
                $('#show-comentario').html(data.comentario_html || '');
                $('#show-created-at').text(data.created_at || '-');

                if (data.arquivo_visualizar_url) {
                    $('#show-arquivo').html(buildFileIconLink(data.arquivo_visualizar_url, data.arquivo_ext));
                } else {
                    $('#show-arquivo').text('-');
                }

                $('#modalComunicacaoShow').modal('show');
            });
        });

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const url = buildUrl(deleteUrlTemplate, id);

            swal({
                title: 'Primeira confirmação',
                text: 'Deseja continuar para a exclusão?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                padding: '2em'
            }).then(function(firstResult) {
                if (!firstResult.value) {
                    return;
                }

                swal({
                    title: 'Confirmação final',
                    text: 'Esta acao nao podera ser desfeita. Confirma excluir?',
                    type: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Excluir',
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Cancelar',
                    cancelButtonColor: '#3085d6',
                    padding: '2em'
                }).then(function(secondResult) {
                    if (!secondResult.value) {
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
                            $('#row-comunicacao-' + id).remove();
                            swal('Sucesso', resp.message || 'Comunicação excluida com sucesso.', 'success');
                        },
                        error: function() {
                            swal('Erro', 'Falha ao excluir registro.', 'error');
                        }
                    });
                });
            });
        });

        $('#formComunicacaoAjax').on('submit', function(e) {
            e.preventDefault();

            const id = $('#comunicacao-id').val();
            const formData = new FormData(this);
            const comentario = getEditorContent();
            const categoriaId = $('#comunicacao-categoria').val();

            if (!categoriaId) {
                swal('Validação', 'Selecione uma categoria.', 'error');
                return;
            }

            if (isRichTextEmpty(comentario)) {
                swal('Validacao', 'O campo Comentário é obrigatório.', 'error');
                return;
            }

            formData.set('categoria_comunicacao_id', categoriaId);
            formData.set('comentario', comentario);

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
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp) {
                    $('#modalComunicacaoForm').modal('hide');
                    swal('Sucesso', resp.message || 'Operacao realizada com sucesso.', 'success');
                    window.location.reload();
                },
                error: function(xhr) {
                    showValidationErrors(xhr);
                }
            });
        });

        if (hasTinyMce()) {
            initEditor();
        }
    })();
</script>
@endsection
