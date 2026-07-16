@extends('template.layout')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Sessão expirada') }}</div>
                <div class="card-body">
                    <p>{{ __('Não foi possível concluir a operação porque sua sessão expirou (erro 419).') }}</p>
                    <p>{{ __('Atualize a página e tente novamente.') }}</p>
                    <a href="{{ url()->previous() }}" class="btn btn-primary">{{ __('Voltar') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
