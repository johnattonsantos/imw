@extends('template.layout')

@section('content')
    @include('extras.alerts')
    @php
        $podeCriar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.criar') && auth()->user()->hasPerfilRegra('patrimonio.juridico');
        $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar') && auth()->user()->hasPerfilRegra('patrimonio.juridico');
        $podeExcluir = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.excluir') && auth()->user()->hasPerfilRegra('patrimonio.juridico');
    @endphp

    <div class="container-fluid d-flex justify-content-between">
        @if ($podeCriar)
            <a href="{{ route('patrimonio.riscos-juridicos.create') }}" class="btn btn-primary mt-3 mb-3 ml-2">NOVO RISCO JURÍDICO</a>
        @endif
    </div>

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Riscos Jurídicos</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-4">
                        <thead>
                            <tr>
                                <th>Imóvel</th>
                                <th>Nível</th>
                                <th>Possui ônus</th>
                                <th>Status</th>
                                <th>Data identificação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riscos as $item)
                                <tr>
                                    <td>{{ $item->imovel?->nome ?: ('Imóvel #' . $item->imovel_id) }}</td>
                                    <td>{{ $item->riscoLabel() }}</td>
                                    <td>{{ $item->possui_onus ? 'Sim' : 'Não' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
                                    <td>{{ optional($item->data_identificacao)->format('d/m/Y') ?: '-' }}</td>
                                    <td class="d-flex" style="gap:.5rem;">
                                        <a href="{{ route('patrimonio.riscos-juridicos.show', $item->id) }}" class="btn btn-sm btn-info btn-rounded">Ver</a>
                                        @if ($podeEditar)
                                            <a href="{{ route('patrimonio.riscos-juridicos.edit', $item->id) }}" class="btn btn-sm btn-dark btn-rounded" title="Editar" aria-label="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if ($podeExcluir)
                                            <form method="POST" action="{{ route('patrimonio.riscos-juridicos.destroy', $item->id) }}" onsubmit="return confirm('Remover risco jurídico?')">
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
                                <tr><td colspan="6" class="text-center">Nenhum risco jurídico cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $riscos->links() }}
            </div>
        </div>
    </div>
@endsection
