@extends('template.layout')

@section('content')
    @php $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar') && auth()->user()->hasPerfilRegra('patrimonio.juridico'); @endphp
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalhes do Risco Jurídico</h5>
                @if ($podeEditar)
                    <a href="{{ route('patrimonio.riscos-juridicos.edit', $riscoJuridico->id) }}" class="btn btn-dark btn-sm">Editar</a>
                @endif
            </div>
            <div class="widget-content widget-content-area">
                <p><strong>Imóvel:</strong> {{ $riscoJuridico->imovel?->nome ?: ('Imóvel #' . $riscoJuridico->imovel_id) }}</p>
                <p><strong>Possui ônus:</strong> {{ $riscoJuridico->possui_onus ? 'Sim' : 'Não' }}</p>
                <p><strong>Tipo de ônus:</strong> {{ $riscoJuridico->tipo_onus ?: '-' }}</p>
                <p><strong>Nível de risco:</strong> {{ $riscoJuridico->riscoLabel() }}</p>
                <p><strong>Data de identificação:</strong> {{ optional($riscoJuridico->data_identificacao)->format('d/m/Y') ?: '-' }}</p>
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $riscoJuridico->status)) }}</p>
                <p><strong>Descrição:</strong> {{ $riscoJuridico->descricao ?: '-' }}</p>
                <p><strong>Providência recomendada:</strong> {{ $riscoJuridico->providencia_recomendada ?: '-' }}</p>

                <a href="{{ route('patrimonio.riscos-juridicos.index') }}" class="btn btn-secondary mt-3">Voltar</a>
            </div>
        </div>
    </div>
@endsection
