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
            <a href="{{ route('patrimonio.bens-moveis.create') }}" class="btn btn-primary mt-3 mb-3 ml-2">NOVO BEM MÓVEL</a>
        @endif
    </div>

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Bens Móveis</h4>
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
                                <th>Categoria</th>
                                <th>Status</th>
                                <th>% Depreciado</th>
                                <th>Valor contábil atual</th>
                                <th>Imóvel</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bensMoveis as $item)
                                <tr>
                                    <td>{{ $item->codigo_patrimonial }}</td>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->categoria ?: '-' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
                                    <td>{{ number_format((float) data_get($item, 'depreciacao.percentual_depreciado', 0), 2, ',', '.') }}%</td>
                                    <td>R$ {{ number_format((float) data_get($item, 'depreciacao.valor_contabil_atual', 0), 2, ',', '.') }}</td>
                                    <td>{{ $item->imovel?->nome ?: '-' }}</td>
                                    <td class="d-flex" style="gap:.5rem;">
                                        <a href="{{ route('patrimonio.bens-moveis.show', $item->id) }}" class="btn btn-sm btn-info btn-rounded" title="Visualizar">Ver</a>
                                        @if ($podeEditar)
                                            <a href="{{ route('patrimonio.bens-moveis.edit', $item->id) }}" class="btn btn-sm btn-dark btn-rounded" title="Editar">Editar</a>
                                        @endif
                                        @if ($podeExcluir)
                                            <form method="POST" action="{{ route('patrimonio.bens-moveis.destroy', $item->id) }}" onsubmit="return confirm('Remover bem móvel?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger btn-rounded" title="Excluir">Excluir</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nenhum bem móvel cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $bensMoveis->links() }}
            </div>
        </div>
    </div>
@endsection
