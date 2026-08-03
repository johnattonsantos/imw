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
            <a href="{{ route('patrimonio.benfeitorias.create') }}" class="btn btn-primary mt-3 mb-3 ml-2">NOVA BENFEITORIA</a>
        @endif
    </div>

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Benfeitorias</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-4">
                        <thead>
                            <tr>
                                <th>Imóvel</th>
                                <th>Descrição</th>
                                <th>Data</th>
                                <th>Valor investido</th>
                                <th>Responsável</th>
                                <th>Anexo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($benfeitorias as $item)
                                <tr>
                                    <td>{{ $item->imovel?->nome ?: ('Imóvel #' . $item->imovel_id) }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($item->descricao, 60) }}</td>
                                    <td>{{ optional($item->data)->format('d/m/Y') ?: '-' }}</td>
                                    <td>{{ $item->valorInvestidoFormatado() }}</td>
                                    <td>{{ $item->responsavel ?: '-' }}</td>
                                    <td>
                                        @if ($item->documento_anexo)
                                            <a href="{{ route('patrimonio.benfeitorias.download', $item->id) }}" class="btn btn-sm btn-outline-primary">Baixar</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="d-flex" style="gap:.5rem;">
                                        <a href="{{ route('patrimonio.benfeitorias.show', $item->id) }}" class="btn btn-sm btn-info btn-rounded">Ver</a>
                                        @if ($podeEditar)
                                            <a href="{{ route('patrimonio.benfeitorias.edit', $item->id) }}" class="btn btn-sm btn-dark btn-rounded" title="Editar" aria-label="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if ($podeExcluir)
                                            <form method="POST" action="{{ route('patrimonio.benfeitorias.destroy', $item->id) }}" onsubmit="return confirm('Remover benfeitoria?')">
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
                                    <td colspan="7" class="text-center">Nenhuma benfeitoria cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $benfeitorias->links() }}
            </div>
        </div>
    </div>
@endsection
