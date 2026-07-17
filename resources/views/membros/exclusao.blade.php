@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Membros', 'url' => '/secretaria/membro/', 'active' => false],
    ['text' => 'Exclusão de Membro', 'url' => '#', 'active' => true]
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
      <form class="form-vertical" method="POST" action="{{ route('membro.exclusao.store', ['id' => $pessoa->id]) }}" enctype="multipart/form-data">
        @csrf
        @method('DELETE')
        <div class="row">
          <div class="col-md-12">
            <div class="alert alert-danger border-0 mb-4" role="alert">
              <strong>
                {{ __('ATENÇÃO!!! Esta ação não pode ser revertida') }} <br>
                {{ __('Após excluir este membro, o mesmo não será mais listado no ROL. Atual no menu de membros, apenas no rol Permanente e Desligados não sendo mais possível alterar seus dados') }}
              </strong>
            </div> 
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Data de Exclusão:') }}</label>
          </div>
          <div class="col-lg-6">
            <input type="date" class="form-control @error('dt_exclusao') is-invalid @enderror" id="dt_exclusao" name="dt_exclusao" value="{{ old('dt_exclusao', date('Y-m-d')) }}" placeholder="{{ __('ex: 31/12/2000') }}">
            @error('dt_exclusao')
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
            @error('modo_recepcao_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div> 

        <div class="form-group mt-4">
          <a href="{{ route('membro.editar', ['id' => $pessoa->id]) }}" class="btn btn-secondary">
            <x-bx-arrow-back/> {{ __('Voltar') }}
          </a>
          <button type="submit" class="btn btn-primary">{{ __('Salvar') }}</button>
        </div>
      </form>
    </div>


</div>
@endsection
@section('extras-scripts')
    <script src="{{ asset('membros/js/editar.js') }}"></script>
@endsection