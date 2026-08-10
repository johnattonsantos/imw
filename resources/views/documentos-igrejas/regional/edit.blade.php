@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => __('Home'), 'url' => '/', 'active' => false],
    ['text' => __('Documentos para Igrejas'), 'url' => route('documentos-igrejas.index'), 'active' => false],
    ['text' => __('Editar Documento'), 'url' => '#', 'active' => true],
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
                    <h4>{{ __('Editar Documento') }}</h4>
                    <p class="pl-3 mb-0">{{ __('Altere o título ou adicione novos documentos ao cadastro.') }}</p>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('documentos-igrejas.update', $documento) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group col-lg-6 col-md-8 col-sm-12">
                        <label for="titulo">{{ __('Título') }} <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $documento->titulo) }}" class="form-control" maxlength="255" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-lg-8 col-md-10 col-sm-12">
                        <label>{{ __('Adicionar documentos') }}</label>
                        <div id="documentos-arquivos-container">
                            <div class="input-group mb-2 documento-arquivo-row">
                                <input type="file" name="arquivos[]" class="form-control" accept="{{ $accept }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary adicionar-arquivo" title="{{ __('Adicionar outro arquivo') }}">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Você pode enviar novos documentos para este mesmo título.') }} {{ __('Formatos permitidos') }}: {{ $formatosPermitidos }}. {{ __('Tamanho máximo por arquivo') }}: 20 MB.
                        </small>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Documentos cadastrados') }}</th>
                                <th style="width: 160px;">{{ __('Ações') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documento->arquivos as $arquivo)
                                <tr>
                                    <td>{{ $arquivo->nome_original }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info btn-rounded bs-tooltip btn-visualizar-documento" title="{{ __('Visualizar') }}" aria-label="{{ __('Visualizar') }}" data-documento-url="{{ route('documentos-igrejas.visualizar', $arquivo) }}" data-documento-nome="{{ $arquivo->nome_original }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('documentos-igrejas.arquivo.destroy', $arquivo) }}" class="d-inline" onsubmit="return confirm('{{ __('Deseja excluir este arquivo?') }}')">
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">{{ __('Nenhum documento cadastrado.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary btn-rounded">{{ __('Atualizar') }}</button>
                <a href="{{ route('documentos-igrejas.index') }}" class="btn btn-light btn-rounded">{{ __('Voltar') }}</a>
            </form>
        </div>
    </div>
</div>
@include('documentos-igrejas._visualizador-modal')
@endsection

@section('extras-scripts')
@include('documentos-igrejas._visualizador-script')
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
