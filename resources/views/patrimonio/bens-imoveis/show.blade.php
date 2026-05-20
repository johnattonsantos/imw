@extends('template.layout')

@section('content')
    @php $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar'); @endphp

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header d-flex justify-content-between align-items-center" style="padding: 1rem;">
                <h4 class="mb-0">Detalhes do Bem Imóvel</h4>
                @if ($podeEditar)
                    <a href="{{ route('patrimonio.bens-imoveis.edit', $imovel->id) }}" class="btn btn-dark btn-sm">Editar</a>
                @endif
            </div>
            <div class="widget-content widget-content-area">
                <p><strong>Código:</strong> {{ $imovel->codigo_patrimonial ?: '-' }}</p>
                <p><strong>Nome:</strong> {{ $imovel->tituloExibicao() }}</p>
                <p><strong>Natureza:</strong> {{ $imovel->natureza_imovel ?: '-' }}</p>
                <p><strong>Endereço:</strong> {{ $imovel->endereco ?: '-' }}</p>
                <p><strong>Cidade/UF:</strong> {{ trim(($imovel->cidade ?: '-') . ' / ' . ($imovel->estado ?: '-')) }}</p>
                <p><strong>Status de titularidade:</strong> {{ $imovel->status_titularidade ?: '-' }}</p>
                <p><strong>Matrícula:</strong> {{ $imovel->numero_matricula ?: '-' }}</p>
                <p><strong>Possui escritura registrada:</strong> {{ $imovel->possui_escritura_registrada ? 'Sim' : 'Não' }}</p>
                <p><strong>Regularização pendente:</strong> {{ $imovel->regularizacao_pendente ? 'Sim' : 'Não' }}</p>
                <p><strong>Valor histórico:</strong> R$ {{ number_format((float) $imovel->valor_historico, 2, ',', '.') }}</p>
                <p><strong>Valor venal:</strong> R$ {{ number_format((float) $imovel->valor_venal, 2, ',', '.') }}</p>
                <p><strong>Valor de mercado:</strong> R$ {{ number_format((float) $imovel->valor_mercado, 2, ',', '.') }}</p>
                <p><strong>Observações jurídicas:</strong> {{ $imovel->observacoes_juridicas ?: '-' }}</p>

                <a href="{{ route('patrimonio.bens-imoveis.index') }}" class="btn btn-secondary mt-3">Voltar</a>
            </div>
        </div>
    </div>
@endsection
