@extends('template.layout')

@section('content')
    @include('extras.alerts')

    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar Bem Móvel</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('patrimonio.bens-moveis.update', $bemMovel->id) }}">
                    @csrf
                    @method('PUT')
                    @include('patrimonio.bens-moveis._form', ['bemMovel' => $bemMovel])

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('patrimonio.bens-moveis.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
