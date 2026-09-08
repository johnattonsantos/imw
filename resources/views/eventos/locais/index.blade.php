@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Locais do Evento', 'url' => route('eventos.locais.index'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Locais do Evento') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('Pesquisar local') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('Filtrar') }}</button>
                    </div>
                </div>
            </form>

            <div class="mb-3">
                <a href="{{ route('eventos.locais.create') }}" class="btn btn-primary btn-sm">{{ __('Novo') }}</a>
                <a href="{{ route('eventos.index') }}" class="btn btn-light btn-sm">{{ __('Eventos') }}</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Local do Evento') }}</th>
                            <th>{{ __('Endereço') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th style="width: 150px;">{{ __('Ações') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($locais as $local)
                            <tr>
                                <td>{{ $local->nome }}</td>
                                <td>{{ $local->endereco ?: '-' }}</td>
                                <td>{{ $local->ativo ? __('Ativo') : __('Inativo') }}</td>
                                <td class="table-action">
                                    <a href="{{ route('eventos.locais.edit', $local) }}" class="btn btn-sm btn-dark btn-rounded bs-tooltip" title="{{ __('Editar') }}" aria-label="{{ __('Editar') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-edit-2">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('eventos.locais.destroy', $local) }}" class="d-inline" onsubmit="return confirm('{{ __('Deseja excluir este local?') }}')">
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
                                <td colspan="4" class="text-center">{{ __('Nenhum local encontrado.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $locais->links('vendor.pagination.index') }}
        </div>
    </div>
</div>
@endsection
