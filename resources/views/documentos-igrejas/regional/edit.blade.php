@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => __('Home'), 'url' => '/', 'active' => false],
    ['text' => __('Documentos para Igrejas'), 'url' => route('documentos-igrejas.index'), 'active' => false],
    ['text' => __('Editar Documento'), 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .select2-container {
        width: 100% !important;
    }
</style>
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
            <form method="POST" action="{{ route('documentos-igrejas.update', $documento) }}" enctype="multipart/form-data" class="documentos-igrejas-form">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group col-lg-6 col-md-8 col-sm-12">
                        <label for="titulo">{{ __('Título') }} <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $documento->titulo) }}" class="form-control" maxlength="255" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                        <label for="destino">{{ __('Destino') }} <span class="text-danger">*</span></label>
                        <select name="destino" id="destino" class="form-control" required>
                            <option value="todas" {{ old('destino', $documento->igreja_id ? 'igreja' : 'todas') === 'todas' ? 'selected' : '' }}>{{ __('Todas as igrejas') }}</option>
                            <option value="igreja" {{ old('destino', $documento->igreja_id ? 'igreja' : 'todas') === 'igreja' ? 'selected' : '' }}>{{ __('Igreja específica') }}</option>
                        </select>
                        <small class="form-text text-muted">
                            {{ __('Todas as igrejas apenas visualizam. Igreja específica visualiza e baixa.') }}
                        </small>
                    </div>
                    <div class="form-group col-lg-5 col-md-6 col-sm-12" id="igreja-destino-wrapper">
                        <label for="igreja_id">{{ __('Igreja específica') }} <span class="text-danger">*</span></label>
                        <select name="igreja_id" id="igreja_id" class="form-control igreja-destino-select">
                            <option value="">{{ __('Selecione') }}</option>
                            @foreach ($igrejas as $igreja)
                                <option value="{{ $igreja->id }}" {{ (string) old('igreja_id', $documento->igreja_id) === (string) $igreja->id ? 'selected' : '' }}>
                                    {{ $igreja->distrito_nome }} - {{ $igreja->nome }}
                                </option>
                            @endforeach
                        </select>
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
                            {{ __('Você pode enviar novos documentos para este mesmo título.') }} {{ __('Formatos permitidos') }}: {{ $formatosPermitidos }}. {{ __('Tamanho máximo por arquivo') }}: {{ $tamanhoMaximoMb }} MB.
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
                                        <button type="submit" form="excluir-arquivo-{{ $arquivo->id }}" class="btn btn-sm btn-danger btn-rounded bs-tooltip" title="{{ __('Excluir') }}" aria-label="{{ __('Excluir') }}">
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

            @foreach ($documento->arquivos as $arquivo)
                <form id="excluir-arquivo-{{ $arquivo->id }}" method="POST" action="{{ route('documentos-igrejas.arquivo.destroy', $arquivo) }}" class="d-none" onsubmit="return confirm('{{ __('Deseja excluir este arquivo?') }}')">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        </div>
    </div>
</div>
@include('documentos-igrejas._visualizador-modal')
@endsection

@section('extras-scripts')
@include('documentos-igrejas._visualizador-script')
<script src="{{ asset('theme/plugins/select2/select2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('documentos-arquivos-container');
        const destino = document.getElementById('destino');
        const igrejaWrapper = document.getElementById('igreja-destino-wrapper');
        const igrejaSelect = document.getElementById('igreja_id');
        const form = document.querySelector('.documentos-igrejas-form');
        const tamanhoMaximoArquivo = {{ (int) $tamanhoMaximoMb }} * 1024 * 1024;
        const mensagemArquivoGrande = @json(__('Cada documento deve ter no máximo :tamanho MB.', ['tamanho' => $tamanhoMaximoMb]));
        const normalizarBusca = function (value) {
            return (value || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        };
        const exibirErroArquivoGrande = function () {
            if (window.toastr) {
                toastr.error(mensagemArquivoGrande);
                return;
            }

            alert(mensagemArquivoGrande);
        };
        const arquivoExcedeLimite = function (input) {
            return Array.from(input.files || []).some(function (file) {
                return file.size > tamanhoMaximoArquivo;
            });
        };

        if (igrejaSelect && window.jQuery && jQuery.fn.select2) {
            jQuery(igrejaSelect).select2({
                placeholder: @json(__('Selecione')),
                allowClear: true,
                width: '100%',
                matcher: function (params, data) {
                    if (!params.term || params.term.trim() === '') {
                        return data;
                    }

                    if (typeof data.text === 'undefined') {
                        return null;
                    }

                    return normalizarBusca(data.text).indexOf(normalizarBusca(params.term)) > -1 ? data : null;
                }
            });
        }

        const toggleIgrejaDestino = function () {
            const isIgreja = destino && destino.value === 'igreja';

            if (igrejaWrapper) {
                igrejaWrapper.style.display = isIgreja ? '' : 'none';
            }

            if (igrejaSelect) {
                igrejaSelect.required = isIgreja;
                if (!isIgreja) {
                    if (window.jQuery && jQuery.fn.select2) {
                        jQuery(igrejaSelect).val(null).trigger('change');
                    } else {
                        igrejaSelect.value = '';
                    }
                }
            }
        };

        if (destino) {
            destino.addEventListener('change', toggleIgrejaDestino);
            toggleIgrejaDestino();
        }

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

        document.addEventListener('change', function (event) {
            const fileInput = event.target.closest('input[type="file"][name="arquivos[]"]');

            if (!fileInput || !arquivoExcedeLimite(fileInput)) {
                return;
            }

            fileInput.value = '';
            exibirErroArquivoGrande();
        });

        if (form) {
            form.addEventListener('submit', function (event) {
                const hasLargeFile = Array.from(form.querySelectorAll('input[type="file"][name="arquivos[]"]'))
                    .some(arquivoExcedeLimite);

                if (hasLargeFile) {
                    event.preventDefault();
                    exibirErroArquivoGrande();
                }
            });
        }
    });
</script>
@endsection
