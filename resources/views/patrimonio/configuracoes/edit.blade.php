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
                    <h4>Editar Configuração - {{ $labelTipo }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="POST" action="{{ route('patrimonio.configuracoes.tipos.update', ['tipo' => $tipo, 'configuracao' => $configuracao->id]) }}">
                @csrf
                @method('PUT')
                @include('patrimonio.configuracoes._form', ['configuracao' => $configuracao])

                <button class="btn btn-success">Atualizar</button>
                <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => $tipo]) }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
