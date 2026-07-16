@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Visitantes', 'url' => '/secretaria/visitante/', 'active' => false],
    ['text' => 'Editar', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Editar Visitante') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            @if($visitante)
            <form class="form-vertical" action="{{ route('visitante.update', $visitante->membro_id ?? '') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group mb-4 col-12">
                        <label class="control-label">{{ __('* Nome') }}</label>
                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" minlength="4" value="{{ old('nome', $visitante->nome ?? '') }}" maxlength="100">
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">{{ __('* Sexo') }}</label>
                        <select name="sexo" class="form-control @error('sexo') is-invalid @enderror">
                            <option value="" {{ old('sexo', $visitante->sexo ?? '') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                            <option value="M" {{ old('sexo', $visitante->sexo ?? '') == 'M' ? 'selected' : '' }}>{{ __('Masculino') }}</option>
                            <option value="F" {{ old('sexo', $visitante->sexo ?? '') == 'F' ? 'selected' : '' }}>{{ __('Feminino') }}</option>
                        </select>
                        @error('sexo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">{{ __('Data de Nascimento') }}</label>
                        <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" name="data_nascimento" value="{{ old('data_nascimento', optional($visitante->data_nascimento)->format('Y-m-d') ?? '') }}">
                        @error('data_nascimento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">{{ __('Data de Conversão') }}</label>
                        <input type="date" class="form-control @error('data_conversao') is-invalid @enderror" name="data_conversao" value="{{ old('data_conversao', $visitante->data_conversao ?? '') }}">
                        @error('data_conversao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('E-mail') }}</label>
                        <input type="email" name="email_preferencial" class="form-control @error('email_preferencial') is-invalid @enderror" value="{{ old('email_preferencial', $visitante->email_preferencial ?? '') }}" maxlength="100">
                        @error('email_preferencial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
          
                    <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('Telefone') }}</label>
                        <input type="text" id="telefone_preferencial" name="telefone_preferencial" class="form-control @error('telefone_preferencial') is-invalid @enderror" placeholder="{{ __('ex: +55 (00) 0000-0000') }}" value="{{ old('telefone_preferencial', $visitante->telefone_preferencial ?? '') }}">
                        @error('telefone_preferencial')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="row">
                    
                  <!--   <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('Telefone Alternativo') }}</label>
                        <input type="text" id="telefone_alternativo" name="telefone_alternativo" class="form-control @error('telefone_alternativo') is-invalid @enderror" value="{{ old('telefone_alternativo', $visitante->telefone_alternativo ?? '') }}">
                        @error('telefone_alternativo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->

                  <!--   <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('Whatsapp') }}</label>
                        <input type="text" id="whatsapp" name="telefone_whatsapp" class="form-control @error('telefone_whatsapp') is-invalid @enderror" value="{{ old('telefone_whatsapp', $visitante->telefone_whatsapp ?? '') }}">
                        @error('telefone_whatsapp')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->

                    <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('Congregação') }}</label>
                        <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" >
                            <option value="" {{ !$visitante->congregacao_id ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                            @foreach ($congregacoes as $congregacao)
                                <option value="{{ $congregacao->id }}" {{$visitante->congregacao_id == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
                            @endforeach
                        </select>
                        @error('congregacao_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('GCEU') }}</label>
                        <select id="gceu_id" name="gceu_id" class="form-control @error('gceu_id') is-invalid @enderror" >
                            <option value="" {{ old('gceu_id') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                            @foreach ($gceus as $gceu)
                                <option value="{{ $gceu->id }}" {{$visitante->gceu_id == $gceu->id ? 'selected' : '' }}>{{ $gceu->nome }}</option>
                            @endforeach
                        </select>
                        @error('gceu_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <input type="submit" value="Atualizar" class="btn btn-primary ml-3 mt-3">
            </form>
            @else
            <div class="alert alert-warning" role="alert">
                {{ __('Visitante não encontrado.') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script src="{{ asset('visitantes/js/editar.js') }}"></script>
@endsection
