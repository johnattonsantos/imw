@php
    $tiposMenu = [
        'natureza' => 'Natureza',
        'status' => 'Status',
        'iptu' => 'IPTU',
        'categoria' => 'Categoria',
        'comprobatorio' => 'Comprobatório',
        'tipo_documento' => 'Tipo de documento',
    ];
    $tipoAtual = $tipoAtual ?? null;
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center" style="gap:.5rem;">
            <a href="{{ route('patrimonio.configuracoes.hub') }}"
               class="btn btn-sm {{ empty($tipoAtual) ? 'btn-primary' : 'btn-outline-primary' }}">
                Visão Geral
            </a>
            @foreach ($tiposMenu as $slug => $label)
                <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => $slug]) }}"
                   class="btn btn-sm {{ $tipoAtual === $slug ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
</div>
