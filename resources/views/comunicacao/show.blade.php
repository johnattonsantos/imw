@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Comunicação', 'url' => '/comunicacao', 'active' => false],
    ['text' => 'Detalhes', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Detalhes da Comunicação</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <p><strong>Categoria:</strong> {{ optional($comunicacao->categoria)->nome ?: '-' }}</p>
            <p><strong>Título:</strong> {{ $comunicacao->titulo }}</p>
            <p><strong>Comentário:</strong></p>
            <div>{!! $comunicacao->comentario !!}</div>
            <p><strong>Arquivo:</strong>
                @if ($comunicacao->arquivo)
                    @php
                        $arquivoExt = strtolower((string) pathinfo($comunicacao->arquivo, PATHINFO_EXTENSION));
                        $iconClass = 'fa-file';
                        $colorClass = 'text-muted';
                        if ($arquivoExt === 'pdf') {
                            $iconClass = 'fa-file-pdf';
                            $colorClass = 'text-danger';
                        } elseif (in_array($arquivoExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                            $iconClass = 'fa-file-image';
                            $colorClass = 'text-info';
                        } elseif (in_array($arquivoExt, ['doc', 'docx'], true)) {
                            $iconClass = 'fa-file-word';
                            $colorClass = 'text-primary';
                        } elseif (in_array($arquivoExt, ['xls', 'xlsx'], true)) {
                            $iconClass = 'fa-file-excel';
                            $colorClass = 'text-success';
                        } elseif (in_array($arquivoExt, ['zip', 'rar'], true)) {
                            $iconClass = 'fa-file-archive';
                            $colorClass = 'text-warning';
                        }
                    @endphp
                    <a href="{{ route('comunicacao.visualizar', $comunicacao) }}" target="_blank" title="Ver arquivo">
                        <i class="fas {{ $iconClass }} {{ $colorClass }} fa-lg"></i>
                    </a>
                @else
                    -
                @endif
            </p>
            <p><strong>Criado em:</strong> {{ optional($comunicacao->created_at)->format('d/m/Y H:i:s') }}</p>

            <a href="{{ route('comunicacao.edit', $comunicacao) }}" class="btn btn-dark">Editar</a>
            <a href="{{ route('comunicacao.index') }}" class="btn btn-light">Voltar</a>
        </div>
    </div>
</div>
@endsection
