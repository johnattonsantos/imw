@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Jurídico Regional', 'url' => route('regiao.juridico.acoes.index'), 'active' => false],
    ['text' => 'Ações Judiciais', 'url' => route('regiao.juridico.acoes.index'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><div class="row"><div class="col-12"><h4>Ações Judiciais</h4></div></div></div>
        <div class="widget-content widget-content-area">
            <div class="mb-3">
                @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-acoes-novo'))
                    <a href="{{ route('regiao.juridico.acoes.create') }}" class="btn btn-primary btn-sm">Nova Ação</a>
                @endif
                <a href="{{ route('regiao.juridico.relatorios') }}" class="btn btn-secondary btn-sm">Relatórios</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Processo</th>
                            <th>Instituição</th>
                            <th>Autor</th>
                            <th>Ré</th>
                            <th>Status</th>
                            <th>Resultado</th>
                            <th style="width: 160px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($acoes as $acao)
                            <tr>
                                <td>{{ $acao->numero_processo ?: '-' }}</td>
                                <td>{{ optional($acao->instituicao)->nome ?: '-' }}</td>
                                <td>{{ $acao->autor }}</td>
                                <td>{{ $acao->reu }}</td>
                                <td>{{ $statusOptions[$acao->status] ?? $acao->status }}</td>
                                <td>{{ $resultadoOptions[$acao->resultado] ?? $acao->resultado }}</td>
                                <td class="table-action">
                                    <a href="{{ route('regiao.juridico.acoes.show', $acao) }}" class="btn btn-sm btn-info btn-rounded" title="Ver">Ver</a>
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-acoes-editar'))
                                        <a href="{{ route('regiao.juridico.acoes.edit', $acao) }}" class="btn btn-sm btn-dark btn-rounded" title="Editar">Editar</a>
                                    @endif
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-acoes-excluir'))
                                        <form action="{{ route('regiao.juridico.acoes.destroy', $acao) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja excluir esta ação judicial?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-rounded">Excluir</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Nenhuma ação judicial cadastrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $acoes->links('vendor.pagination.index') }}
        </div>
    </div>
</div>
@endsection
