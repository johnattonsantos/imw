@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Novo', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')
@include('extras.alerts-error-all')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Novo Evento</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('eventos.store') }}">
                @csrf
                @include('eventos._form')
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="{{ route('eventos.index') }}" class="btn btn-light">Voltar</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
@include('eventos._scripts')
@endsection
