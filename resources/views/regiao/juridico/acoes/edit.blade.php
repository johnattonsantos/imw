@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Jurídico Regional', 'url' => route('regiao.juridico.acoes.index'), 'active' => false],
    ['text' => 'Editar Ação Judicial', 'url' => route('regiao.juridico.acoes.edit', $acao), 'active' => true],
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><div class="row"><div class="col-12"><h4>Editar Ação Judicial</h4></div></div></div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('regiao.juridico.acoes.update', $acao) }}">
                @csrf
                @method('PUT')
                @include('regiao.juridico.acoes._form')
            </form>
        </div>
    </div>
</div>
@endsection
