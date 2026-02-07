@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Comunicacao', 'url' => '/comunicacao', 'active' => false],
    ['text' => 'Detalhes', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Detalhes da Comunicacao</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <p><strong>Titulo:</strong> {{ $comunicacao->titulo }}</p>
            <p><strong>Comentario:</strong></p>
            <div class="border p-3 mb-3">{!! $comunicacao->comentario !!}</div>
            <p><strong>Arquivo:</strong>
                @if ($comunicacao->arquivo)
                    <a href="{{ route('comunicacao.visualizar', $comunicacao) }}" target="_blank">Visualizar</a>
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
