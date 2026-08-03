@extends('template.layout')

@section('content')
    @php $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar') && auth()->user()->hasPerfilRegra('patrimonio.documentos'); @endphp
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalhes do Documento</h5>
                <div>
                    @if ($podeEditar)
                        <a href="{{ route('patrimonio.documentos.edit', $documento->id) }}" class="btn btn-dark btn-sm">Editar</a>
                    @endif
                    <a href="{{ route('patrimonio.documentos.download', $documento->id) }}" class="btn btn-primary btn-sm">Baixar</a>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <p><strong>Nome:</strong> {{ $documento->nome }}</p>
                <p><strong>Tipo:</strong> {{ $documento->tipo }}</p>
                <p><strong>Status:</strong> {{ ucfirst($documento->status) }}</p>
                <p><strong>Data de emissão:</strong> {{ optional($documento->data_emissao)->format('d/m/Y') ?: '-' }}</p>
                <p><strong>Data de validade:</strong> {{ optional($documento->data_validade)->format('d/m/Y') ?: '-' }}</p>
                <p><strong>Alerta de vencimento:</strong> {{ $documento->alerta_vencimento ? 'Sim (até 30 dias)' : 'Não' }}</p>
                <p><strong>Vínculo:</strong>
                    @if ($documento->documentavel_type === \App\Models\Patrimonio\Imovel::class)
                        Imóvel #{{ $documento->documentavel_id }}
                    @elseif ($documento->documentavel_type === \App\Models\Patrimonio\BemMovel::class)
                        Bem móvel #{{ $documento->documentavel_id }}
                    @else
                        -
                    @endif
                </p>
                <p><strong>Observações:</strong> {{ $documento->observacoes ?: '-' }}</p>

                <a href="{{ route('patrimonio.documentos.index') }}" class="btn btn-secondary mt-3">Voltar</a>
            </div>
        </div>
    </div>
@endsection
