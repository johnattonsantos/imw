@extends('template.layout')

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-12">
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area text-center py-5">
                    <h4>Olá {{ $nomeUsuario }} bem-vindo ao seu perfil</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
