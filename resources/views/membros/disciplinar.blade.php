@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Membros', 'url' => '/secretaria/membro/', 'active' => false],
    ['text' => 'Disciplina de Membro', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
        <div class="row">
            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
              <h4>{{ $pessoa->nome }}</h4>
            </div>
        </div>
    </div>

    <div class="widget-content widget-content-area">
      <form class="form-vertical" method="POST" action="{{ route('membro.disciplinar.store', ['id' => $pessoa->id]) }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="alert alert-warning border-0 mb-4" role="alert">
              <strong>
                {{ __('ATENÇÃO!!! ESTA AÇÃO NÃO PODE SER REVERTIDA.') }}<br>
                {{ __('Continue apenas se tiver certeza disso.') }}
              </strong>
            </div>
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Data de Início:') }}</label>
          </div>
          <div class="col-lg-6">
            <input type="date" class="form-control @error('dt_inicio') is-invalid @enderror" id="dt_inicio" name="dt_inicio" value="{{ old('dt_inicio', date('Y-m-d')) }}" placeholder="{{ __('ex: 31/12/2000') }}">
            @error('dt_inicio')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Data de Término:') }}</label>
          </div>
          <div class="col-lg-6">
            <input type="date" class="form-control @error('dt_termino') is-invalid @enderror" id="dt_termino" name="dt_termino" value="{{ old('dt_termino') }}" placeholder="{{ __('ex: 31/12/2000') }}">
            @error('dt_termino')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Modo:') }}</label>
          </div>
          <div class="col-lg-6">
            <select id="modo_exclusao_id" name="modo_exclusao_id" class="form-control @error('modo_exclusao_id') is-invalid @enderror" >
              <option value="" {{ old('modo_exclusao_id') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
              @foreach ($modos as $modo)
                <option value="{{ $modo->id }}" {{ old('modo_exclusao_id') == $modo->id ? 'selected' : '' }}>{{ $modo->nome }}</option>
              @endforeach
            </select>
              @error('modo_exclusao_id')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Pastor:') }}</label>
          </div>
          <div class="col-lg-6">
            <select id="clerigo_id" name="clerigo_id" class="form-control @error('clerigo_id') is-invalid @enderror" >
              <option value="" {{ old('clerigo_id') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
              @foreach ($pastores as $pastor)
                <option value="{{ $pastor->id }}" {{ old('clerigo_id') == $pastor->id ? 'selected' : '' }}>{{ $pastor->nome }}</option>
              @endforeach
            </select>
            @error('clerigo_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Observação:') }}</label>
          </div>
          <div class="col-lg-6">
            <textarea id="observacao" name="observacao" class="form-control @error('observacao') is-invalid @enderror"  rows="4"></textarea>

            @error('modo_recepcao_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group mt-4">
          <a href="{{ route('membro.editar', ['id' => $pessoa->id]) }}" class="btn btn-secondary">
            <x-bx-arrow-back/> {{ __('Voltar') }}
          </a>
          <button type="submit" class="btn btn-warning">
            <x-bx-block/> {{ __('Disciplinar') }}
          </button>
        </div>
      </form>
    </div>


</div>
@endsection
@section('extras-scripts')
    <script src="{{ asset('membros/js/editar.js') }}"></script>
@endsection