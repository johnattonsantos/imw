@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => __('Home'), 'url' => '/', 'active' => false],
    ['text' => __('Documentos'), 'url' => route('documentos-local.index'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Documentos') }}</h4>
                    <p class="pl-3 mb-0">{{ __('Documentos cadastrados pela região para visualização da igreja local.') }}</p>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4">
                    <thead>
                        <tr>
                            <th>{{ __('Título') }}</th>
                            <th>{{ __('Data do cadastramento') }}</th>
                            <th>{{ __('Arquivos') }}</th>
                            <th style="width: 160px;">{{ __('Ações') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documentos as $documento)
                            @php
                                $arquivosCount = $documento->arquivos->count();
                                $primeiroArquivo = $documento->arquivos->first();
                            @endphp
                            <tr>
                                <td>{{ $documento->titulo }}</td>
                                <td>{{ optional($documento->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $documento->arquivos->count() }}</td>
                                <td>
                                    @if ($arquivosCount === 1 && $primeiroArquivo)
                                        <button type="button" class="btn btn-sm btn-info btn-rounded bs-tooltip mb-1 btn-visualizar-documento" title="{{ __('Visualizar') }}" aria-label="{{ __('Visualizar') }}" data-documento-url="{{ route('documentos-local.visualizar', $primeiroArquivo) }}" data-documento-nome="{{ $primeiroArquivo->nome_original }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    @elseif ($arquivosCount > 1)
                                        <button type="button" class="btn btn-sm btn-info btn-rounded bs-tooltip mb-1" title="{{ __('Visualizar') }}" aria-label="{{ __('Visualizar') }}" data-toggle="modal" data-target="#documentosModal{{ $documento->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('Nenhum documento cadastrado.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @foreach ($documentos as $documento)
                @if ($documento->arquivos->count() > 1)
                    <div class="modal fade" id="documentosModal{{ $documento->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('Lista de documentos') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Fechar') }}">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-3"><strong>{{ $documento->titulo }}</strong></p>
                                    <div class="list-group">
                                        @foreach ($documento->arquivos as $arquivo)
                                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center btn-visualizar-documento" data-documento-url="{{ route('documentos-local.visualizar', $arquivo) }}" data-documento-nome="{{ $arquivo->nome_original }}">
                                                <span>{{ $arquivo->nome_original }}</span>
                                                <span class="btn btn-sm btn-info btn-rounded">{{ __('Visualizar') }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light btn-rounded" data-dismiss="modal">{{ __('Fechar') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            {{ $documentos->links('vendor.pagination.index') }}
        </div>
    </div>
</div>
@include('documentos-igrejas._visualizador-modal')
@endsection

@section('extras-scripts')
@include('documentos-igrejas._visualizador-script')
@endsection
