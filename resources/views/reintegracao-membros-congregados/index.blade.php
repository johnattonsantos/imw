@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Reintegração de membros e congregados', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@include('extras.alerts-error-all')
@include('extras.alerts')

@php
    $destinoMembro = \App\Models\MembresiaMembro::VINCULO_MEMBRO;
    $destinoCongregado = \App\Models\MembresiaMembro::VINCULO_CONGREGADO;
    $resultadoStatus = $resultado['status'] ?? null;
    $pessoa = $resultado['pessoa'] ?? null;
    $destinoSelecionado = old('destino', $destinoCongregado);
@endphp

<div class="statbox widget box box-shadow">
  <div class="widget-header">
    <div class="row">
      <div class="col-xl-12 col-md-12 col-sm-12 col-12">
        <h4>{{ __('Reintegração de membros e congregados') }}</h4>
      </div>
    </div>
  </div>

  <div class="widget-content widget-content-area">
    <form class="form-vertical mb-4" method="GET" action="{{ route('reintegracao-membros-congregados.index') }}">
      <div class="form-group row mb-4">
        <div class="col-lg-2 text-right">
          <label class="control-label">{{ __('CPF:') }}</label>
        </div>
        <div class="col-lg-5">
          <input type="text" class="form-control cpf-mask" id="cpf_busca" name="cpf" value="{{ old('cpf', $cpf) }}" placeholder="000.000.000-00" autocomplete="off">
        </div>
        <div class="col-lg-3">
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-search"></i> {{ __('Buscar') }}
          </button>
        </div>
      </div>
    </form>

    @if($resultadoStatus && $resultadoStatus !== 'apta')
      <div class="alert alert-warning border-0 mb-4" role="alert">
        {{ $resultado['message'] }}
      </div>
    @endif

    @if($resultadoStatus === 'apta' && $pessoa)
      <div class="alert alert-info border-0 mb-4" role="alert">
        {{ __('Pessoa localizada. Confira os dados e escolha como ela será recebida na igreja atual.') }}
        @if(!empty($resultado['ultima_exclusao']))
          <br>{{ __('Última data de exclusão:') }} {{ $resultado['ultima_exclusao']->format('d/m/Y') }}
        @endif
      </div>

      <form class="form-vertical" method="POST" action="{{ route('reintegracao-membros-congregados.store') }}">
        @csrf
        <input type="hidden" name="membro_id" value="{{ $pessoa->id }}">
        <input type="hidden" name="cpf" value="{{ $cpf }}">

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Nome:') }}</label>
          </div>
          <div class="col-lg-8">
            <input type="text" class="form-control" value="{{ $pessoa->nome }}" readonly>
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">{{ __('Como a pessoa será recebida?') }}</label>
          </div>
          <div class="col-lg-8 d-flex align-items-center flex-wrap">
            <label class="mr-4 mb-2">
              <input type="radio" name="destino" value="{{ $destinoCongregado }}" {{ $destinoSelecionado === $destinoCongregado ? 'checked' : '' }}>
              {{ __('Congregado') }}
            </label>
            <label class="mr-4 mb-2">
              <input type="radio" name="destino" value="{{ $destinoMembro }}" {{ $destinoSelecionado === $destinoMembro ? 'checked' : '' }}>
              {{ __('Membro') }}
            </label>
            @error('destino')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div id="campos-membro" style="display: {{ $destinoSelecionado === $destinoMembro ? 'block' : 'none' }};">
          <div class="form-group row mb-4">
            <div class="col-lg-2 text-right">
              <label class="control-label">{{ __('* Nº do Rol:') }}</label>
            </div>
            <div class="col-lg-5">
              <input type="number" class="form-control @error('numero_rol') is-invalid @enderror" id="numero_rol" name="numero_rol" value="{{ old('numero_rol', $resultado['sugestao_rol'] ?? '') }}">
              @error('numero_rol')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="form-group row mb-4">
            <div class="col-lg-2 text-right">
              <label class="control-label">{{ __('* Data de recepção:') }}</label>
            </div>
            <div class="col-lg-5">
              <input type="date" class="form-control @error('dt_recepcao') is-invalid @enderror" id="dt_recepcao" name="dt_recepcao" value="{{ old('dt_recepcao', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
              @error('dt_recepcao')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="form-group row mb-4">
            <div class="col-lg-2 text-right">
              <label class="control-label">{{ __('* Modo de recepção:') }}</label>
            </div>
            <div class="col-lg-5">
              <select id="modo_recepcao_id" name="modo_recepcao_id" class="form-control @error('modo_recepcao_id') is-invalid @enderror">
                <option value="">{{ __('Selecione') }}</option>
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
              <label class="control-label">{{ __('Pastor:') }}</label>
            </div>
            <div class="col-lg-5">
              <select id="clerigo_id" name="clerigo_id" class="form-control @error('clerigo_id') is-invalid @enderror">
                <option value="">{{ __('Selecione') }}</option>
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
              <label class="control-label">{{ __('Congregação:') }}</label>
            </div>
            <div class="col-lg-5">
              <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror">
                <option value="">{{ __('Sede') }}</option>
                @foreach ($congregacoes as $congregacao)
                  <option value="{{ $congregacao->id }}" {{ old('congregacao_id') == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
                @endforeach
              </select>
              @error('congregacao_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <div class="form-group mt-4">
          <button type="submit" class="btn btn-primary">{{ __('Confirmar reintegração') }}</button>
        </div>
      </form>
    @endif
  </div>
</div>
@endsection

@section('extras-scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    function mascaraCpf(input) {
      input.addEventListener('input', function () {
        let value = input.value.replace(/\D/g, '').slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        input.value = value;
      });
    }

    document.querySelectorAll('.cpf-mask').forEach(mascaraCpf);

    const camposMembro = document.getElementById('campos-membro');
    document.querySelectorAll('input[name="destino"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        camposMembro.style.display = this.value === '{{ $destinoMembro }}' ? 'block' : 'none';
      });
    });
  });
</script>
@endsection
