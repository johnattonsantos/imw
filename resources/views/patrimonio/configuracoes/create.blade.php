@extends('template.layout')

@section('content')
@include('extras.alerts')

<div class="col-lg-12 col-12">
    @include('patrimonio.configuracoes._menu-tipos', ['tipoAtual' => $tipo])
</div>

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Nova Configuração - {{ $labelTipo }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('patrimonio.configuracoes.tipos.store', ['tipo' => $tipo]) }}">
                @csrf
                @include('patrimonio.configuracoes._form')

                <button class="btn btn-success">Salvar</button>
                <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => $tipo]) }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
