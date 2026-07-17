@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Membros', 'url' => '/secretaria/membro/', 'active' => false],
    ['text' => 'Reintegrar Membro', 'url' => '#', 'active' => true]
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
      <form class="form-vertical" method="POST" action="{{ route('membro.reintegrar.store', ['id' => $pessoa->id]) }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="alert alert-warning border-0 mb-4" role="alert">
              <strong>
                {{ __('ATENÇÃO!!! Esta ação não pode ser revertida.') }}<br>
                {{ __('Após Reintegrar este membro DESLIGADO ao Rol Atual, ou seja, membros ativos, por Reconciliação ou Adesão o mesmo passará a ser listado no menu de membros em ROL Atual e só deixará de ser ativo se for novamente Excluído ou Transferido') }}
              </strong>
            </div>
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Nº do Rol:') }}</label>
          </div>
          <div class="col-lg-6">
            <input type="number" class="form-control @error('numero_rol') is-invalid @enderror" id="numero_rol" name="numero_rol" value="{{ old('numero_rol', $sugestaoRol) }}" readonly>
            @error('numero_rol')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Data de Reintegração:') }}</label>
          </div>
          <div class="col-lg-6">
            <input type="date" class="form-control @error('dt_recepcao') is-invalid @enderror" id="dt_recepcao" name="dt_recepcao" value="{{ old('dt_recepcao', date('Y-m-d')) }}" placeholder="{{ __('ex: 31/12/2000') }}">
            @error('dt_recepcao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Modo:') }}</label>
          </div>
          <div class="col-lg-6">
            <select id="modo_recepcao_id" name="modo_recepcao_id" class="form-control @error('modo_recepcao_id') is-invalid @enderror" >
              <option value="" {{ old('modo_recepcao_id') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
              @foreach ($modos as $modo)
                <option value="{{ $modo->id }}" {{ old('modo_recepcao_id') == $modo->id ? 'selected' : '' }}>{{ $modo->nome }}</option>
              @endforeach
            </select>
              @error('modo_recepcao_id')
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

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Congregação:') }}</label>
          </div>
          <div class="col-lg-6">
            <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" >
              <option value="" {{ old('congregacao_id') == '' ? 'selected' : '' }}>{{ __('Selecione') }}</option>
              @foreach ($congregacoes as $congregacao)
                <option value="{{ $congregacao->id }}" {{ old('congregacao_id') == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
              @endforeach
            </select>
            @error('modo_recepcao_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group mt-4">
          <button type="submit" class="btn btn-primary">{{ __('Salvar') }}</button>
        </div>
      </form>
    </div>


</div>
@endsection
@section('extras-scripts')
    <script src="{{ asset('membros/js/editar.js') }}"></script>
@endsection