@extends('template.layout')

@section('content')
    @php $podeEditar = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.editar'); @endphp
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detalhes do Bem Móvel</h5>
                @if ($podeEditar)
                    <a href="{{ route('patrimonio.bens-moveis.edit', $bemMovel->id) }}" class="btn btn-dark btn-sm">Editar</a>
                @endif
            </div>
            <div class="widget-content widget-content-area">
                <div class="row">
                    <div class="col-md-8">
                        <p><strong>Código patrimonial:</strong> {{ $bemMovel->codigo_patrimonial }}</p>
                        <p><strong>Nome:</strong> {{ $bemMovel->nome }}</p>
                        <p><strong>Categoria:</strong> {{ $bemMovel->categoria ?: '-' }}</p>
                        <p><strong>Descrição:</strong> {{ $bemMovel->descricao ?: '-' }}</p>
                        <p><strong>Estado de conservação:</strong> {{ $bemMovel->estado_conservacao ?: '-' }}</p>
                        <p><strong>Localização:</strong> {{ $bemMovel->localizacao ?: '-' }}</p>
                        <p><strong>Responsável:</strong> {{ $bemMovel->responsavel ?: '-' }}</p>
                        <p><strong>Data de aquisição:</strong> {{ optional($bemMovel->data_aquisicao)->format('d/m/Y') ?: '-' }}</p>
                        <p><strong>Valor de aquisição:</strong> {{ isset($bemMovel->valor_aquisicao) ? number_format((float) $bemMovel->valor_aquisicao, 2, ',', '.') : '-' }}</p>
                        <p><strong>Valor residual:</strong> {{ isset($bemMovel->valor_residual) ? number_format((float) $bemMovel->valor_residual, 2, ',', '.') : '-' }}</p>
                        <p><strong>Vida útil:</strong> {{ $bemMovel->vida_util !== null ? $bemMovel->vida_util . ' anos' : '-' }}</p>
                        <p><strong>Natureza comprobatória:</strong> {{ $bemMovel->natureza_comprobatoria ?: '-' }}</p>
                        <p><strong>Número do documento:</strong> {{ $bemMovel->numero_documento ?: '-' }}</p>
                        <p><strong>Fornecedor/Doador:</strong> {{ $bemMovel->fornecedor_doador ?: '-' }}</p>
                        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $bemMovel->status)) }}</p>
                        <hr>
                        <h6>Depreciação</h6>
                        <p><strong>Depreciação anual:</strong> R$ {{ number_format((float) data_get($depreciacao, 'depreciacao_anual', 0), 2, ',', '.') }}</p>
                        <p><strong>Depreciação acumulada:</strong> R$ {{ number_format((float) data_get($depreciacao, 'depreciacao_acumulada', 0), 2, ',', '.') }}</p>
                        <p><strong>Valor contábil atual:</strong> R$ {{ number_format((float) data_get($depreciacao, 'valor_contabil_atual', 0), 2, ',', '.') }}</p>
                        <p><strong>Percentual depreciado:</strong> {{ number_format((float) data_get($depreciacao, 'percentual_depreciado', 0), 2, ',', '.') }}%</p>
                        <p><strong>Situação:</strong> {{ data_get($depreciacao, 'status_depreciado') ? 'Depreciado' : 'Em depreciação' }}</p>
                        <p><strong>Imóvel vinculado:</strong> {{ $bemMovel->imovel?->nome ?: '-' }}</p>
                        <p><strong>Observações:</strong> {{ $bemMovel->observacoes ?: '-' }}</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6>QR Code Patrimonial</h6>
                        <img src="{{ $bemMovel->qr_code_url }}" alt="QR Code Patrimonial" style="max-width:220px; width:100%; border:1px solid #ddd; padding:8px; border-radius:6px;">
                    </div>
                </div>

                <a href="{{ route('patrimonio.bens-moveis.index') }}" class="btn btn-secondary mt-3">Voltar</a>
            </div>
        </div>
    </div>
@endsection
