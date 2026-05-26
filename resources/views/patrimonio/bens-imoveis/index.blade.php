@extends('template.layout')

@section('content')
    @include('extras.alerts')
    @php
        $podeCriar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.criar');
        $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar');
        $podeExcluir = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.excluir');
    @endphp

    <div class="container-fluid d-flex justify-content-between">
        @if ($podeCriar)
            <a href="{{ route('patrimonio.bens-imoveis.create') }}" class="btn btn-primary mt-3 mb-3 ml-2">NOVO BEM IMÓVEL</a>
        @endif
    </div>

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Bens Imóveis</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-4">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Natureza</th>
                                <th>Cidade/UF</th>
                                <th>Status titularidade</th>
                                <th>Regularização</th>
                                <th>Valor mercado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($imoveis as $item)
                                <tr>
                                    <td>{{ $item->codigo_patrimonial ?: '-' }}</td>
                                    <td>{{ $item->tituloExibicao() }}</td>
                                    <td>{{ $item->natureza_imovel ?: '-' }}</td>
                                    <td>{{ trim(($item->cidade ?: '-') . ' / ' . ($item->estado ?: '-')) }}</td>
                                    <td>{{ $item->status_titularidade ?: '-' }}</td>
                                    <td>
                                        @if ($item->regularizacao_pendente)
                                            <span class="badge badge-danger">Pendente</span>
                                        @else
                                            <span class="badge badge-success">Regular</span>
                                        @endif
                                    </td>
                                    <td>R$ {{ number_format((float) $item->valor_mercado, 2, ',', '.') }}</td>
                                    <td class="d-flex" style="gap:.5rem;">
                                        <a href="{{ route('patrimonio.bens-imoveis.show', $item->id) }}" class="btn btn-sm btn-info btn-rounded">Ver</a>
                                        @if ($podeEditar)
                                            <a href="{{ route('patrimonio.bens-imoveis.edit', $item->id) }}" class="btn btn-sm btn-dark btn-rounded" title="Editar" aria-label="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if ($podeExcluir)
                                            <form method="POST" action="{{ route('patrimonio.bens-imoveis.destroy', $item->id) }}" onsubmit="return confirm('Remover bem imóvel?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger btn-rounded" title="Excluir" aria-label="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nenhum bem imóvel cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $imoveis->links() }}
            </div>
        </div>
    </div>
@endsection
