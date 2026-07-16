@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Visitantes', 'url' => '/visitante/', 'active' => true]
]"></x-breadcrumb>
@endsection
@section('content')
<div class="container-fluid">
    
</div>
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Novo Visitante') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            
        </div>
    </div>
</div>
@endsection