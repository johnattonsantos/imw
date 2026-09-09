@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Jurídico Regional', 'url' => route('regiao.juridico.acoes.index'), 'active' => false],
    ['text' => 'Advogados', 'url' => route('regiao.juridico.advogados.index'), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><div class="row"><div class="col-12"><h4>Advogados</h4></div></div></div>
        <div class="widget-content widget-content-area">
            <div class="mb-3">
                @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-advogados-novo'))
                    <a href="{{ route('regiao.juridico.advogados.create') }}" class="btn btn-primary btn-sm">Novo Advogado</a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>OAB</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th>Endereço do Escritório</th>
                            <th style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($advogados as $advogado)
                            <tr>
                                <td>{{ $advogado->nome }}</td>
                                <td>{{ $tipoOptions[$advogado->tipo] ?? $advogado->tipo }}</td>
                                <td>{{ $advogado->registro_oab ?: '-' }}</td>
                                <td>{{ $advogado->telefone ?: '-' }}</td>
                                <td>{{ $advogado->email ?: '-' }}</td>
                                <td>{{ $advogado->endereco_escritorio ?: '-' }}</td>
                                <td>
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-advogados-editar'))
                                        <a href="{{ route('regiao.juridico.advogados.edit', $advogado) }}" class="btn btn-sm btn-dark btn-rounded">Editar</a>
                                    @endif
                                    @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-advogados-excluir'))
                                        <form action="{{ route('regiao.juridico.advogados.destroy', $advogado) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja excluir este advogado?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger btn-rounded">Excluir</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Nenhum advogado cadastrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $advogados->links('vendor.pagination.index') }}
        </div>
    </div>
</div>
@endsection
