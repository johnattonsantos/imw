@extends('template.layout')

@section('content')
    @include('extras.alerts')
    @php
        $podeCriar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.criar') && auth()->user()->hasPerfilRegra('patrimonio.documentos');
        $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar') && auth()->user()->hasPerfilRegra('patrimonio.documentos');
        $podeExcluir = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.excluir') && auth()->user()->hasPerfilRegra('patrimonio.documentos');
    @endphp

    <div class="container-fluid d-flex justify-content-between">
        @if ($podeCriar)
            <a href="{{ route('patrimonio.documentos.create') }}" class="btn btn-primary mt-3 mb-3 ml-2">NOVO DOCUMENTO</a>
        @endif
    </div>

    @if ($alertaVencimentoCount > 0)
        <div class="col-lg-12 col-12">
            <div class="alert alert-warning">
                Atenção: {{ $alertaVencimentoCount }} documento(s) vencem nos próximos 30 dias.
            </div>
        </div>
    @endif

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Documentos Patrimoniais</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-4">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Vínculo</th>
                                <th>Validade</th>
                                <th>Status</th>
                                <th>Alerta</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documentos as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->tipo }}</td>
                                    <td>
                                        @if ($item->documentavel_type === \App\Models\Patrimonio\Imovel::class)
                                            Imóvel
                                        @elseif ($item->documentavel_type === \App\Models\Patrimonio\BemMovel::class)
                                            Bem móvel
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ optional($item->data_validade)->format('d/m/Y') ?: '-' }}</td>
                                    <td>{{ ucfirst($item->status) }}</td>
                                    <td>
                                        @if ($item->alerta_vencimento)
                                            <span class="badge badge-warning">Vence em até 30 dias</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="d-flex" style="gap:.5rem;">
                                        <a href="{{ route('patrimonio.documentos.show', $item->id) }}" class="btn btn-sm btn-info btn-rounded">Ver</a>
                                        @if ($podeEditar)
                                            <a href="{{ route('patrimonio.documentos.edit', $item->id) }}" class="btn btn-sm btn-dark btn-rounded" title="Editar" aria-label="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('patrimonio.documentos.download', $item->id) }}" class="btn btn-sm btn-primary btn-rounded">Arquivo</a>
                                        @if ($podeExcluir)
                                            <form method="POST" action="{{ route('patrimonio.documentos.destroy', $item->id) }}" onsubmit="return confirm('Remover documento?')">
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
                                    <td colspan="7" class="text-center">Nenhum documento patrimonial cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $documentos->links() }}
            </div>
        </div>
    </div>
@endsection
