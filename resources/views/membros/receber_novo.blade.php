@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Membros', 'url' => '/secretaria/membro/', 'active' => false],
    ['text' => 'Recebimento de Novo Membro', 'url' => '#', 'active' => true]
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
      <form id="membro-receber-novo-form" class="form-vertical" method="POST" action="{{ route('membro.receber_novo.store', ['id' => $pessoa->id]) }}" enctype="multipart/form-data">
        @csrf
          <input type="hidden" name="cpf" value="{{ $pessoa->cpf }}">

        <div class="row">
          <div class="col-md-12">
            <div class="alert alert-warning border-0 mb-4" role="alert">
              <strong>Esta ação não pode ser revertida. Após receber este congregado como membro, o mesmo estará listado no menu de membros e não mais em congregados</strong>
            </div>
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">* Nº do Rol:</label>
          </div>
          <div class="col-lg-6">
            <input type="number" class="form-control @error('numero_rol') is-invalid @enderror" id="numero_rol" name="numero_rol" value="{{ old('numero_rol', $sugestaoRol) }}">
            @error('numero_rol')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('cpf')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">* Data de Recepção:</label>
          </div>
          <div class="col-lg-6">
            <input type="date" class="form-control @error('dt_recepcao') is-invalid @enderror" id="dt_recepcao" name="dt_recepcao" value="{{ old('dt_recepcao', date('Y-m-d')) }}" placeholder="ex: 31/12/2000">
            @error('dt_recepcao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">* Modo:</label>
          </div>
          <div class="col-lg-6">
            <select id="modo_recepcao_id" name="modo_recepcao_id" class="form-control @error('modo_recepcao_id') is-invalid @enderror" >
              <option value="" {{ old('modo_recepcao_id') == '' ? 'selected' : '' }}>Selecione</option>
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
            <label class="control-label">* Pastor:</label>
          </div>
          <div class="col-lg-6">
            <select id="clerigo_id" name="clerigo_id" class="form-control @error('clerigo_id') is-invalid @enderror" >
              <option value="" {{ old('clerigo_id') == '' ? 'selected' : '' }}>Selecione</option>
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
            <label class="control-label">Congregação:</label>
          </div>
          <div class="col-lg-6">
            <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" >
              <option value="" {{ old('congregacao_id') == '' ? 'selected' : '' }}>Selecione</option>
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
          <a href="{{ route('congregado.editar', ['id' => $pessoa->id]) }}" class="btn btn-secondary">
            <x-bx-arrow-back/>
            Voltar
          </a>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>


</div>
@endsection
@section('extras-scripts')
    <script src="{{ asset('membros/js/editar.js') }}"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('membro-receber-novo-form');
        if (!form) {
          return;
        }

        form.addEventListener('submit', function (event) {
          if (form.dataset.submitting === '1') {
            event.preventDefault();
            return;
          }

          form.dataset.submitting = '1';

          const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
          submitButtons.forEach(function (button) {
            button.disabled = true;
            if (button.tagName === 'BUTTON') {
              button.dataset.originalText = button.dataset.originalText || button.innerHTML;
              button.innerHTML = 'Processando...';
            } else {
              button.dataset.originalText = button.dataset.originalText || button.value;
              button.value = 'Processando...';
            }
          });
        });
      });
    </script>
@endsection
