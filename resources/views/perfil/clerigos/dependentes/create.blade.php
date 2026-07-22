@extends('template.layout')
@section('breadcrumb')
  <x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Dependentes', 'url' => '/clerigos/perfil/dependentes', 'active' => false],
    ['text' => 'Novo Dependente', 'url' => '', 'active' => true],
  ]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />

  <style>
    .swal2-popup .swal2-styled.swal2-cancel {
      color: white !important;
    }
  </style>
@endsection

@section('content')
  @include('extras.alerts')

  <!-- TABELA -->
  <div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
      <div class="widget-header">
        <div class="row">
          <div class="col-xl-12 col-md-12 col-sm-12 col-12">
            <h4>{{ __('Novo Dependente') }}</h4>
          </div>
        </div>
      </div>

      <div class="widget-content widget-content-area">
        <form class="form-vertical" action="{{ route('clerigos.perfil.dependentes.store') }}" method="POST">
          @csrf
          <div class="row">
            <div class="form-group mb-4 col-sm-12 col-md-6">
              <label class="control-label">{{ __('* Nome') }}</label>
              <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"  minlength="4" value="{{ old('nome') }}" maxlength="100">
              @error('nome')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group mb-4 col-12 col-sm-6 col-md-3">
              <label class="control-label">{{ __('* CPF') }}</label>
              <input type="text" name="cpf" id="cpf" class="form-control @error('cpf') is-invalid @enderror" value="{{ old('cpf') }}">
              @error('cpf')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group mb-4 col-12 col-sm-6 col-md-3">
              <label class="control-label">{{ __('Data de Nascimento') }}</label>
              <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" placeholder="{{ __('ex: 31/12/2000') }}">
              @error('data_nascimento')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="form-group mb-4  col-12 col-sm-6 col-md-3">
              <label class="control-label">{{ __('* Grau de Parentesco') }}</label>
              <select id="parentesco" name="parentesco" class="form-control @error('parentesco') is-invalid @enderror" >
                <option value="" {{ old('parentesco') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                <option value="Cônjuge" {{ old('parentesco') == 'Cônjuge' ? 'selected' : '' }}>{{ __('Cônjuge') }}</option>
                <option value="Filho(a)" {{ old('parentesco') == 'Filho(a)' ? 'selected' : '' }}>{{ __('Filho(a)') }}</option>
              </select>
              @error('parentesco')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group mb-4  col-12 col-sm-6 col-md-3">
              <label class="control-label">{{ __('* Sexo') }}</label>
              <select id="sexo" name="sexo" class="form-control @error('sexo') is-invalid @enderror" >
                <option value="" {{ old('sexo') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>{{ __('Masculino') }}</option>
                <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>{{ __('Feminino') }}</option>
              </select>
              @error('sexo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group mb-4  col-sm-12 col-md-6">
              <label class="control-label">{{ __('* declarar como dependente em seu IRPF?') }}</label>
              <select id="declarar_em_irpf" name="declarar_em_irpf" class="form-control @error('declarar_em_irpf') is-invalid @enderror" >
                <option value="" {{ old('declarar_em_irpf') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
                <option value="1" {{ old('declarar_em_irpf') == '1' ? 'selected' : '' }}>{{ __('Sim') }}</option>
                <option value="0" {{ old('declarar_em_irpf') == '0' ? 'selected' : '' }}>{{ __('Não') }}</option>
              </select>
              @error('declarar_em_irpf')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <a href="{{ route('clerigos.perfil.dependentes.index') }}" class="btn btn-secondary">
                <x-bx-arrow-back/> {{ __('Voltar') }}
              </a>
              <button type="submit" class="btn btn-primary">
                <x-bx-save/> {{ __('Salvar') }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('extras-scripts')
  <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
  <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
  <script>
    $(document).ready(function () {
      $('#cpf').mask('000.000.000-00');
    });
  </script>
@endsection
