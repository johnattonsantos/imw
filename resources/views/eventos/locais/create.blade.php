@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Locais do Evento', 'url' => route('eventos.locais.index'), 'active' => false],
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
                    <h4>{{ __('Novo Local do Evento') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('eventos.locais.store') }}">
                @csrf
                @include('eventos.locais._form')
                <button type="submit" class="btn btn-primary">{{ __('Salvar') }}</button>
                <a href="{{ route('eventos.locais.index') }}" class="btn btn-light">{{ __('Voltar') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
