@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Comunicação', 'url' => '/comunicacao', 'active' => false],
    ['text' => 'Editar', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')
@include('extras.alerts-error-all')

@section('extras-scripts')
<script src="{{ asset('gceu/tinymce/tinymce.min.js') }}?time={{ time() }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hasTinyMce = typeof window.tinymce !== 'undefined';
        if (!hasTinyMce) {
            return;
        }

        window.tinymce.init({
            selector: '#comentario',
            height: 320,
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

        const form = document.querySelector('form[action="{{ route('comunicacao.update', $comunicacao) }}"]');
        if (form) {
            form.addEventListener('submit', function () {
                window.tinymce.triggerSave();
            });

            form.addEventListener('submit', function (e) {
                const comentario = document.getElementById('comentario');
                const plain = (comentario ? comentario.value : '')
                    .replace(/<[^>]*>/g, '')
                    .replace(/&nbsp;/gi, ' ')
                    .trim();

                if (!plain) {
                    e.preventDefault();
                    alert('O campo Comentário é obrigatório.');
                }
            });
        }
    });
</script>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Editar Comunicação</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('comunicacao.update', $comunicacao) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="categoria_comunicacao_id">* Categoria</label>
                    <select name="categoria_comunicacao_id" id="categoria_comunicacao_id" class="form-control @error('categoria_comunicacao_id') is-invalid @enderror" required>
                        <option value="">Selecione</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ (string) old('categoria_comunicacao_id', $comunicacao->categoria_comunicacao_id) === (string) $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="titulo">* Título</label>
                    <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror"
                        value="{{ old('titulo', $comunicacao->titulo) }}" required>
                </div>

                <div class="form-group">
                    <label for="comentario">* Comentário</label>
                    <textarea name="comentario" id="comentario" rows="6" class="form-control @error('comentario') is-invalid @enderror">{{ old('comentario', $comunicacao->comentario) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="arquivo">Arquivo</label>
                    <input type="file" name="arquivo" id="arquivo" class="form-control @error('arquivo') is-invalid @enderror"
                        accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.zip,.rar">
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
                        <small class="d-block mt-2">
                            Arquivo atual:
                            <a href="{{ route('comunicacao.visualizar', $comunicacao) }}" target="_blank" title="Ver arquivo">
                                <i class="fas {{ $iconClass }} {{ $colorClass }} fa-lg"></i>
                            </a>
                        </small>
                    @endif
                    <small class="text-muted d-block mt-2">Formatos: PDF, imagem, Word, Excel, ZIP, RAR. Tamanho maximo: 10MB</small>
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="{{ route('comunicacao.index') }}" class="btn btn-light">Voltar</a>
            </form>
        </div>
    </div>
</div>
@endsection
