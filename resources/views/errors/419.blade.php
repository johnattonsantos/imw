@extends('template.layout')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Sessão expirada</div>
                <div class="card-body">
                    <p>Não foi possível concluir a operação porque sua sessão expirou (erro 419).</p>
                    <p>Atualize a página e tente novamente.</p>
                    <a href="{{ url()->previous() }}" class="btn btn-primary">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
