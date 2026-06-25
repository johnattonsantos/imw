@extends('template.layout')

@section('content')
    @php $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar') && auth()->user()->hasPerfilRegra('patrimonio.baixa'); @endphp
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalhes da Baixa Patrimonial</h5>
                @if ($podeEditar)
                    <a href="{{ route('patrimonio.baixas.edit', $baixa->id) }}" class="btn btn-dark btn-sm">Editar</a>
                @endif
            </div>
            <div class="widget-content widget-content-area">
                <p><strong>Bem móvel:</strong> {{ $baixa->bemMovel?->nome ?: ('Bem móvel #' . $baixa->bem_movel_id) }}</p>
                <p><strong>Código patrimonial:</strong> {{ $baixa->bemMovel?->codigo_patrimonial ?: '-' }}</p>
                <p><strong>Status atual do bem:</strong> {{ $baixa->bemMovel ? ucfirst(str_replace('_', ' ', $baixa->bemMovel->status)) : '-' }}</p>
                <p><strong>Motivo:</strong> {{ $baixa->motivoFormatado() }}</p>
                <p><strong>Data da baixa:</strong> {{ optional($baixa->data_baixa)->format('d/m/Y') ?: '-' }}</p>
                <p><strong>Responsável:</strong> {{ $baixa->responsavel ?: '-' }}</p>
                <p><strong>Observações:</strong> {{ $baixa->observacoes ?: '-' }}</p>

                @if ($baixa->documento_comprobatorio)
                    <a href="{{ route('patrimonio.baixas.download', $baixa->id) }}" class="btn btn-outline-primary">Baixar documento comprobatório</a>
                @endif

                <a href="{{ route('patrimonio.baixas.index') }}" class="btn btn-secondary mt-3">Voltar</a>
            </div>
        </div>
    </div>
@endsection
