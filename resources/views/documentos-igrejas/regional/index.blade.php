@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => __('Home'), 'url' => '/', 'active' => false],
    ['text' => __('Documentos para Igrejas'), 'url' => route('documentos-igrejas.index'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row align-items-center">
                <div class="col-xl-8 col-md-8 col-sm-12 col-12">
                    <h4>{{ __('Documentos para Igrejas') }}</h4>
                    <p class="pl-3 mb-0">{{ __('Cadastre documentos da região para visualização das igrejas locais.') }}</p>
                </div>
                <div class="col-xl-4 col-md-4 col-sm-12 col-12 text-md-right mt-3 mt-md-0 pr-4">
                    <a href="{{ route('documentos-igrejas.create') }}" class="btn btn-primary btn-sm btn-rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        {{ __('Novo Documento') }}
                    </a>
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
                                <td>{{ $documento->arquivos_count }}</td>
                                <td>
                                    @if ($arquivosCount === 1 && $primeiroArquivo)
                                        <button type="button" class="btn btn-sm btn-info btn-rounded bs-tooltip mb-1 btn-visualizar-documento" title="{{ __('Visualizar') }}" aria-label="{{ __('Visualizar') }}" data-documento-url="{{ route('documentos-igrejas.visualizar', $primeiroArquivo) }}" data-documento-nome="{{ $primeiroArquivo->nome_original }}">
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
                                    <a href="{{ route('documentos-igrejas.edit', $documento) }}" class="btn btn-sm btn-dark btn-rounded bs-tooltip mb-1" title="{{ __('Editar') }}" aria-label="{{ __('Editar') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-edit-2">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('documentos-igrejas.destroy', $documento) }}" class="d-inline" onsubmit="return confirm('{{ __('Deseja excluir este documento?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger btn-rounded bs-tooltip mb-1" title="{{ __('Excluir') }}" aria-label="{{ __('Excluir') }}">
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
                                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center btn-visualizar-documento" data-documento-url="{{ route('documentos-igrejas.visualizar', $arquivo) }}" data-documento-nome="{{ $arquivo->nome_original }}">
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
