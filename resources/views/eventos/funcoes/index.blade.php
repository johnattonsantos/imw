@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Funções Eventos', 'url' => route('eventos.funcoes.index'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Funções Eventos</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Pesquisar função">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="mb-3">
                @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-funcao-novo'))
                    <a href="{{ route('eventos.funcoes.create') }}" class="btn btn-primary btn-sm">Novo</a>
                @endif
                <a href="{{ route('eventos.index') }}" class="btn btn-light btn-sm">Eventos</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Função</th>
                            <th>Status</th>
                            <th style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($funcoes as $funcao)
                            <tr>
                                <td>{{ $funcao->nome }}</td>
                                <td>{{ $funcao->ativo ? 'Ativo' : 'Inativo' }}</td>
                                <td class="table-action">
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-funcao-editar'))
                                        <a href="{{ route('eventos.funcoes.edit', $funcao) }}" class="btn btn-sm btn-dark btn-rounded bs-tooltip" title="Editar" aria-label="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-edit-2">
                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-funcao-excluir'))
                                        <form method="POST" action="{{ route('eventos.funcoes.destroy', $funcao) }}" class="d-inline" onsubmit="return confirm('Deseja excluir esta função?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-rounded bs-tooltip" title="Excluir" aria-label="Excluir">
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
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Nenhuma função encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $funcoes->links('vendor.pagination.index') }}
        </div>
    </div>
</div>
@endsection
