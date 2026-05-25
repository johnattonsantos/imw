@extends('template.layout')

@section('content')
@include('extras.alerts')
@php
    $podeCriar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.criar');
    $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar');
    $podeExcluir = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.excluir');
@endphp

<div class="container-fluid d-flex justify-content-between">
    <a href="{{ route('patrimonio.configuracoes.hub') }}" class="btn btn-secondary mt-3 mb-3 ml-2">VOLTAR</a>
@if ($podeCriar)
        <a href="{{ route('patrimonio.configuracoes.tipos.create', ['tipo' => $tipo]) }}" class="btn btn-primary mt-3 mb-3 ml-2">NOVA CONFIGURAÇÃO</a>
    @endif
</div>

<div class="col-lg-12 col-12">
    @include('patrimonio.configuracoes._menu-tipos', ['tipoAtual' => $tipo])
</div>

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Configurações Globais - {{ $labelTipo }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Ativo</th>
                            <th>Ordem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($configuracoes as $item)
                        <tr>
                            <td>{{ $item->nome }}</td>
                            <td>{{ $item->descricao ?: '-' }}</td>
                            <td>{!! $item->ativo ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>' !!}</td>
                            <td>{{ (int) $item->ordem }}</td>
                            <td class="d-flex" style="gap:.5rem;">
                                @if ($podeEditar)
                                    <a href="{{ route('patrimonio.configuracoes.tipos.edit', ['tipo' => $tipo, 'configuracao' => $item->id]) }}" class="btn btn-sm btn-dark btn-rounded">Editar</a>
                                @endif
                                @if ($podeExcluir)
                                    <form method="POST" action="{{ route('patrimonio.configuracoes.tipos.destroy', ['tipo' => $tipo, 'configuracao' => $item->id]) }}" onsubmit="return confirm('Remover configuração?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger btn-rounded">Excluir</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhuma configuração cadastrada.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $configuracoes->links() }}
        </div>
    </div>
</div>
@endsection
