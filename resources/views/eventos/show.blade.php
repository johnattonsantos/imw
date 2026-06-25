@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Detalhes', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Detalhes do Evento</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            @include('eventos._show_content')

            @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-editar'))
                <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-dark">Editar</a>
            @endif
            <a href="{{ route('eventos.index') }}" class="btn btn-light">Voltar</a>
        </div>
    </div>
</div>
@endsection
