@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Membros', 'url' => '/secretaria/membro/', 'active' => false],
    ['text' => 'Recebimento de Membro Externo', 'url' => '#', 'active' => true]
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
      <form class="form-vertical" method="POST" action="{{ route('membro.receber_membro_externo.store', ['notificacao' => $notificacao->id]) }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="alert alert-dark border-0 mb-4" role="alert">
              <strong>
                {{ __('ATENÇÃO!!! ESTA AÇÃO NÃO PODE SER REVERTIDA.') }}<br> 
                {{ __('Após receber este membro de outra igreja, o mesmo será excluído da igreja de origem.') }}<br>
              </strong>
            </div> 
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Nº do Rol:') }}</label>
          </div>
          <div class="col-lg-6">
            <input type="number" class="form-control @error('numero_rol') is-invalid @enderror" id="numero_rol" name="numero_rol" value="{{ old('numero_rol', $sugestaoRol) }}">
            @error('numero_rol')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('* Data:') }}</label>
          </div>
          <div class="col-lg-6">
            <input type="date" class="form-control @error('dt_resposta') is-invalid @enderror" id="dt_resposta" name="dt_resposta" value="{{ old('dt_resposta', date('Y-m-d')) }}" placeholder="{{ __('ex: 31/12/2000') }}">
            @error('dt_resposta')
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
          <a href="{{ route('membro.editar', ['id' => $pessoa->id]) }}" class="btn btn-secondary">
            <x-bx-arrow-back/> {{ __('Voltar') }}
          </a>

          <button type="submit" name="action" value="accept" class="btn btn-success">
            <x-bx-transfer-alt/> {{ __('Aceitar') }}
          </button>

          <button type="submit" name="action" value="reject" class="btn btn-danger">
            <x-bx-block/> {{ __('Rejeitar') }}
          </button>
        </div>

      </form>
    </div>


</div>
@endsection
@section('extras-scripts')
    <script>
    </script>
@endsection