@extends('template.layout')

@section('content')
    @php $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar'); @endphp
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalhes da Benfeitoria</h5>
                @if ($podeEditar)
                    <a href="{{ route('patrimonio.benfeitorias.edit', $benfeitoria->id) }}" class="btn btn-dark btn-sm">Editar</a>
                @endif
            </div>
            <div class="widget-content widget-content-area">
                <p><strong>Imóvel:</strong> {{ $benfeitoria->imovel?->nome ?: ('Imóvel #' . $benfeitoria->imovel_id) }}</p>
                <p><strong>Descrição:</strong> {{ $benfeitoria->descricao ?: '-' }}</p>
                <p><strong>Data:</strong> {{ optional($benfeitoria->data)->format('d/m/Y') ?: '-' }}</p>
                <p><strong>Valor investido:</strong> {{ $benfeitoria->valorInvestidoFormatado() }}</p>
                <p><strong>Responsável:</strong> {{ $benfeitoria->responsavel ?: '-' }}</p>
                <p><strong>Observações:</strong> {{ $benfeitoria->observacoes ?: '-' }}</p>
                <p><strong>Valor histórico atual do imóvel:</strong> R$ {{ number_format((float) ($benfeitoria->imovel?->valor_historico ?? 0), 2, ',', '.') }}</p>

                @if ($benfeitoria->documento_anexo)
                    <a href="{{ route('patrimonio.benfeitorias.download', $benfeitoria->id) }}" class="btn btn-outline-primary">Baixar anexo</a>
                @endif

                <a href="{{ route('patrimonio.benfeitorias.index') }}" class="btn btn-secondary mt-3">Voltar</a>
            </div>
        </div>
    </div>
@endsection
