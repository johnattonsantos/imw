@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Visitantes', 'url' => '/secretaria/visitante/', 'active' => false],
    ['text' => 'Novo', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection
@include('extras.alerts')
@section('content')
<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Novo Visitante') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" action="{{ route('visitante.store') }}" id="form_create_visitantes" method="post">
                @csrf
                <div class="row">
                    <div class="form-group mb-4 col-12">
                        <label class="control-label">{{ __('* Nome') }}</label>
                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"  minlength="4" value="{{ old('nome') }}" maxlength="100">
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">{{ __('* Sexo') }}</label>
                        <select name="sexo" class="form-control @error('sexo') is-invalid @enderror" >
                            <option value="" {{ old('sexo') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                            <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>{{ __('Masculino') }}</option>
                            <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>{{ __('Feminino') }}</option>
                        </select>
                        @error('sexo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">{{ __('Data de Nascimento') }}</label>
                        <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" placeholder="{{ __('ex: 31/12/2000') }}">
                        @error('data_nascimento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">{{ __('Data de Conversão') }}</label>
                        <input type="date" class="form-control @error('data_conversao') is-invalid @enderror" id="data_conversao" name="data_conversao" value="{{ old('data_conversao') }}" placeholder="{{ __('ex: 31/12/2000') }}">
                        @error('data_conversao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <!-- <div class="form-group mb-4 col-md-4">
                        <label class="control-label">{{ __('Telefone Alternativo') }}</label>
                        <input id="telefone_alternativo" name="telefone_alternativo" type="text" class="form-control @error('telefone_alternativo') is-invalid @enderror" placeholder="{{ __('ex: (00) 0000-0000') }}" value="{{ old('telefone_alternativo') }}">
                        @error('telefone_alternativo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->
                    <!-- div class="form-group mb-4 col-md-4">
                        <label class="control-label">{{ __('Whatsapp') }}</label>
                        <input id="telefone_whatsapp" name="telefone_whatsapp" type="text" class="form-control @error('telefone_whatsapp') is-invalid @enderror" placeholder="{{ __('ex: (00) 00000-0000') }}" value="{{ old('telefone_whatsapp') }}">
                        @error('telefone_whatsapp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->
                </div>
                <div class="row">
                    <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('E-mail') }}</label>
                        <input id="email_preferencial" name="email_preferencial" type="email" class="form-control @error('email_preferencial') is-invalid @enderror" value="{{ old('email_preferencial') }}" maxlength="100">
                        @error('email_preferencial')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('Telefone') }}</label>
                        <input id="telefone_preferencial" name="telefone_preferencial" type="text" class="form-control @error('telefone_preferencial') is-invalid @enderror" placeholder="{{ __('ex: +55 (00) 0000-0000') }}" value="{{ old('telefone_preferencial') }}">
                        @error('telefone_preferencial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group mb-4 col-md-6">
                        <label class="control-label">{{ __('Congregação') }}</label>
                        <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" >
                            <option value="" {{ old('congregacao_id') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                            @foreach ($congregacoes as $congregacao)
                                <option value="{{ $congregacao->id }}" {{ old('congregacao_id') == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
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
                                <option value="{{ $gceu->id }}" {{ old('gceu_id') == $gceu->id ? 'selected' : '' }}>{{ $gceu->nome }}</option>
                            @endforeach
                        </select>
                        @error('gceu_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <input type="submit" value="Salvar" class="btn btn-primary btn-lg mt-3">
            </form>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script src="{{ asset('visitantes/js/create.js') }}?t={{ time() }}"></script>
@endsection
