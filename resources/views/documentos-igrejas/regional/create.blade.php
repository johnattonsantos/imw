@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => __('Home'), 'url' => '/', 'active' => false],
    ['text' => __('Documentos para Igrejas'), 'url' => route('documentos-igrejas.index'), 'active' => false],
    ['text' => __('Novo Documento'), 'url' => '#', 'active' => true],
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
                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                        <label for="destino">{{ __('Destino') }} <span class="text-danger">*</span></label>
                        <select name="destino" id="destino" class="form-control" required>
                            <option value="todas" {{ old('destino', 'todas') === 'todas' ? 'selected' : '' }}>{{ __('Todas as igrejas') }}</option>
                            <option value="igreja" {{ old('destino') === 'igreja' ? 'selected' : '' }}>{{ __('Igreja específica') }}</option>
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
                                <option value="{{ $igreja->id }}" {{ (string) old('igreja_id') === (string) $igreja->id ? 'selected' : '' }}>
                                    {{ $igreja->distrito_nome }} - {{ $igreja->nome }}
                                </option>
                            @endforeach
                        </select>
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
<script src="{{ asset('theme/plugins/select2/select2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('documentos-arquivos-container');
        const destino = document.getElementById('destino');
        const igrejaWrapper = document.getElementById('igreja-destino-wrapper');
        const igrejaSelect = document.getElementById('igreja_id');
        const normalizarBusca = function (value) {
            return (value || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
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
    });
</script>
@endsection
