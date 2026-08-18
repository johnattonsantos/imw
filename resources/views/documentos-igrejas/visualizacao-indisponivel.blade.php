@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => __('Home'), 'url' => '/', 'active' => false],
    ['text' => __('Documentos'), 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Pré-visualização indisponível') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="alert alert-warning mb-0">
                {{ __('Este arquivo não pode ser visualizado pelo navegador.') }}
            </div>
        </div>
    </div>
</div>
@endsection
