@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Comunicacao', 'url' => '/comunicacao', 'active' => false],
    ['text' => 'Editar', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')
@include('extras.alerts-error-all')

@section('extras-scripts')
<script src="{{ asset('gceu/tinymce/tinymce.min.js') }}?time={{ time() }}"></script>
<script>
    tinymce.init({
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
</script>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Editar Comunicacao</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('comunicacao.update', $comunicacao) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="titulo">* Titulo</label>
                    <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror"
                        value="{{ old('titulo', $comunicacao->titulo) }}" required>
                </div>

                <div class="form-group">
                    <label for="comentario">* Comentario</label>
                    <textarea name="comentario" id="comentario" rows="6" class="form-control @error('comentario') is-invalid @enderror" required>{{ old('comentario', $comunicacao->comentario) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="arquivo">Arquivo</label>
                    <input type="file" name="arquivo" id="arquivo" class="form-control @error('arquivo') is-invalid @enderror">
                    @if ($comunicacao->arquivo)
                        <small class="d-block mt-2">Arquivo atual: <a href="{{ route('comunicacao.visualizar', $comunicacao) }}" target="_blank">Visualizar</a></small>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="{{ route('comunicacao.index') }}" class="btn btn-light">Voltar</a>
            </form>
        </div>
    </div>
</div>
@endsection
