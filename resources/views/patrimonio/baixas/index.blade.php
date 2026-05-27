@extends('template.layout')

@section('content')
    @include('extras.alerts')
    @php
        $podeCriar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.criar') && auth()->user()->hasPerfilRegra('patrimonio.baixa');
        $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar') && auth()->user()->hasPerfilRegra('patrimonio.baixa');
        $podeExcluir = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.excluir') && auth()->user()->hasPerfilRegra('patrimonio.baixa');
    @endphp

    <div class="container-fluid d-flex justify-content-between">
        @if ($podeCriar)
            <a href="{{ route('patrimonio.baixas.create') }}" class="btn btn-primary mt-3 mb-3 ml-2">NOVA BAIXA PATRIMONIAL</a>
        @endif
    </div>

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Baixas Patrimoniais</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-4">
                        <thead>
                            <tr>
                                <th>Bem móvel</th>
                                <th>Motivo</th>
                                <th>Data da baixa</th>
                                <th>Responsável</th>
                                <th>Documento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($baixas as $item)
                                <tr>
                                    <td>
                                        {{ $item->bemMovel?->nome ?: ('Bem móvel #' . $item->bem_movel_id) }}
                                        @if ($item->bemMovel?->codigo_patrimonial)
                                            <br><small class="text-muted">{{ $item->bemMovel->codigo_patrimonial }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->motivoFormatado() }}</td>
                                    <td>{{ optional($item->data_baixa)->format('d/m/Y') ?: '-' }}</td>
                                    <td>{{ $item->responsavel ?: '-' }}</td>
                                    <td>
                                        @if ($item->documento_comprobatorio)
                                            <a href="{{ route('patrimonio.baixas.download', $item->id) }}" class="btn btn-sm btn-outline-primary">Baixar</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="d-flex" style="gap:.5rem;">
                                        <a href="{{ route('patrimonio.baixas.show', $item->id) }}" class="btn btn-sm btn-info btn-rounded">Ver</a>
                                        @if ($podeEditar)
                                            <a href="{{ route('patrimonio.baixas.edit', $item->id) }}" class="btn btn-sm btn-dark btn-rounded" title="Editar" aria-label="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if ($podeExcluir)
                                            <form method="POST" action="{{ route('patrimonio.baixas.destroy', $item->id) }}" onsubmit="return confirm('Remover baixa patrimonial?')">
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
                                    <td colspan="6" class="text-center">Nenhuma baixa patrimonial registrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $baixas->links() }}
            </div>
        </div>
    </div>
@endsection
