@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Jurídico Regional', 'url' => route('regiao.juridico.acoes.index'), 'active' => false],
    ['text' => 'Detalhes da Ação Judicial', 'url' => route('regiao.juridico.acoes.show', $acao), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><div class="row"><div class="col-12"><h4>Detalhes da Ação Judicial</h4></div></div></div>
        <div class="widget-content widget-content-area">
            <div class="row">
                <div class="col-md-4"><strong>Processo:</strong><br>{{ $acao->numero_processo ?: '-' }}</div>
                <div class="col-md-4"><strong>Instituição:</strong><br>{{ optional($acao->instituicao)->nome ?: '-' }}</div>
                <div class="col-md-4"><strong>Vara ou Tribunal:</strong><br>{{ $acao->vara_tribunal ?: '-' }}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6"><strong>Autor:</strong><br>{{ $acao->autor }}</div>
                <div class="col-md-6"><strong>Ré:</strong><br>{{ $acao->reu }}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4"><strong>Advogado da causa:</strong><br>{{ optional($acao->advogadoCausa)->nome ?: '-' }}</div>
                <div class="col-md-4"><strong>Advogado da oposição:</strong><br>{{ optional($acao->advogadoOposicao)->nome ?: ($acao->advogado_oposicao_nome ?: '-') }}</div>
                <div class="col-md-4"><strong>Custo da demanda:</strong><br>{{ $acao->custo_demanda !== null ? 'R$ ' . number_format((float) $acao->custo_demanda, 2, ',', '.') : '-' }}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3"><strong>Status:</strong><br>{{ $statusOptions[$acao->status] ?? $acao->status }}</div>
                <div class="col-md-3"><strong>Resultado:</strong><br>{{ $resultadoOptions[$acao->resultado] ?? $acao->resultado }}</div>
                <div class="col-md-3"><strong>Data inicial:</strong><br>{{ optional($acao->data_distribuicao)->format('d/m/Y') ?: '-' }}</div>
                <div class="col-md-3"><strong>Data da sentença:</strong><br>{{ optional($acao->data_sentenca)->format('d/m/Y') ?: '-' }}</div>
            </div>
            <hr>
            <p><strong>Objeto:</strong><br>{{ $acao->objeto ?: '-' }}</p>
            <p><strong>Teor da decisão:</strong><br>{{ $acao->teor_decisao ?: '-' }}</p>
            <p><strong>Outros:</strong><br>{{ $acao->outros ?: '-' }}</p>
            <p><strong>Observações:</strong><br>{{ $acao->observacoes ?: '-' }}</p>
            <div class="d-flex justify-content-between">
                <a href="{{ route('regiao.juridico.acoes.index') }}" class="btn btn-light">Voltar</a>
                @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-acoes-editar'))
                    <a href="{{ route('regiao.juridico.acoes.edit', $acao) }}" class="btn btn-primary">Editar</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
