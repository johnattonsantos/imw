@extends('template.layout')

@section('content')
    @include('extras.alerts')

    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar Benfeitoria</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('patrimonio.benfeitorias.update', $benfeitoria->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('patrimonio.benfeitorias._form', ['benfeitoria' => $benfeitoria])

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('patrimonio.benfeitorias.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
