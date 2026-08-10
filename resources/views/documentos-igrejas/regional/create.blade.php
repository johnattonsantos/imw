@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => __('Home'), 'url' => '/', 'active' => false],
    ['text' => __('Documentos para Igrejas'), 'url' => route('documentos-igrejas.index'), 'active' => false],
    ['text' => __('Novo Documento'), 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')
@include('extras.alerts-error-all')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Criar upload de documentos') }}</h4>
                    <p class="pl-3 mb-0">{{ __('Informe um título e selecione um ou mais documentos para as igrejas da região.') }}</p>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('documentos-igrejas.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="form-group col-lg-6 col-md-8 col-sm-12">
                        <label for="titulo">{{ __('Título') }} <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" class="form-control" maxlength="255" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-lg-8 col-md-10 col-sm-12">
                        <label>{{ __('Documentos') }} <span class="text-danger">*</span></label>
                        <div id="documentos-arquivos-container">
                            <div class="input-group mb-2 documento-arquivo-row">
                                <input type="file" name="arquivos[]" class="form-control" accept="{{ $accept }}" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary adicionar-arquivo" title="{{ __('Adicionar outro arquivo') }}">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Você pode enviar um ou mais documentos para o mesmo título.') }} {{ __('Formatos permitidos') }}: {{ $formatosPermitidos }}. {{ __('Tamanho máximo por arquivo') }}: 20 MB.
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-rounded">{{ __('Salvar') }}</button>
                <a href="{{ route('documentos-igrejas.index') }}" class="btn btn-light btn-rounded">{{ __('Voltar') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('documentos-arquivos-container');

        if (!container) {
            return;
        }

        container.addEventListener('click', function (event) {
            const addButton = event.target.closest('.adicionar-arquivo');
            const removeButton = event.target.closest('.remover-arquivo');

            if (addButton) {
                const row = document.createElement('div');
                row.className = 'input-group mb-2 documento-arquivo-row';
                row.innerHTML = `
                    <input type="file" name="arquivos[]" class="form-control" accept="{{ $accept }}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remover-arquivo" title="{{ __('Remover arquivo') }}">
                            -
                        </button>
                    </div>
                `;
                container.appendChild(row);
            }

            if (removeButton) {
                removeButton.closest('.documento-arquivo-row').remove();
            }
        });
    });
</script>
@endsection
