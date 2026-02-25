@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'GCEU', 'url' => '/gceu/lista', 'active' => false],
    ['text' => 'Carta Pastoral', 'url' => '/gceu/carta-pastoral/', 'active' => false],
    ['text' => 'Novo', 'url' => '#', 'active' => true]
]"></x-breadcrumb>

@endsection
@include('extras.alerts')
@section('content')
<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Nova carta pastoral da Igreja: <u>{{ $instituicao }}</u></h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" action="{{ route('gceu.carta-pastoral.store') }}" id="form_create_gceu" method="post">
                @csrf
                <div class="row">
                    <div class="form-group mb-4 col-12">
                        <label class="control-label" for="pessoa_id">* Pastor</label>
                        <select id="pessoa_id" name="pessoa_id" class="form-control " required=""  class="form-control @error('pessoa_id') is-invalid @enderror" id="pessoa_id" required placeholder="pessoa_id" value="{{ old('pessoa_id') }}">
                            <option value="" selected="">Selecione</option>
                                @foreach($pastores as $pastor)
                                <option value="{{ $pastor->id }}">{{ $pastor->nome }}</option>
                                @endforeach
                        </select>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-12">
                        <label class="control-label" for="titulo">* Título</label>
                        <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror" id="titulo" required placeholder="Título" minlength="4" value="{{ old('titulo') }}" maxlength="150">
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-12">
                        <label class="control-label" for="conteudo">* Carta Pastoral</label>
                        <textarea name="conteudo" id="conteudo" class="form-control @error('conteudo') is-invalid @enderror" placeholder="Introdução" value="{{ old('conteudo') }}"></textarea>
                        @error('conteudo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <input type="submit" value="Salvar" class="btn btn-primary btn-lg mt-3">
            </form>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
    <script src="{{ asset('gceu/tinymce/tinymce.min.js') }}?time={{ time() }}"></script>
    <script>
        const uploadImageUrl = "{{ route('gceu.carta-pastoral.upload-image') }}";
        const csrfToken = "{{ csrf_token() }}";

        tinymce.init({
					selector: 'textarea',
					height: 250,
					menubar: true,
					language: "pt_BR",
					theme: "modern",
					plugins: [
					'advlist autolink lists link image charmap print preview anchor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime media table contextmenu paste code'
					],
					toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent forecolor | image link",
					relative_urls : false,
					remove_script_host : false,
					image_advtab: true,
					content_css: ['//www.tinymce.com/css/codepen.min.css'],
                    automatic_uploads: true,
                    images_upload_handler: function (blobInfo, success, failure, progress) {
                        const xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', uploadImageUrl);
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

                        xhr.upload.onprogress = function (event) {
                            progress(event.loaded / event.total * 100);
                        };

                        xhr.onload = function () {
                            if (xhr.status < 200 || xhr.status >= 300) {
                                failure('Falha no upload: HTTP ' + xhr.status);
                                return;
                            }

                            const json = JSON.parse(xhr.responseText || '{}');
                            if (!json.location) {
                                failure('Resposta inválida do servidor');
                                return;
                            }

                            success(json.location);
                        };

                        xhr.onerror = function () {
                            failure('Falha no upload da imagem.');
                        };

                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    }
		});
    </script>
@endsection
