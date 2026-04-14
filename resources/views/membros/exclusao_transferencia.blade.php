@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Secretaria', 'url' => '/', 'active' => false],
    ['text' => 'Membros', 'url' => '/secretaria/membro/', 'active' => false],
    ['text' => 'Exclusão por Transferência', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
        .select2-selection__rendered {
            line-height: 50px !important;
            padding-left: 25px !important;
            font-family: 'Nunito', sans-serif;
        }

        .select2-container .select2-selection--single {
            height: 50px !important;
            font-weight: 600;
            font-family: 'Nunito', sans-serif;
        }

        .select2-selection__arrow {
            height: 50px !important;
            font-family: 'Nunito', sans-serif;
        }
    </style>
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
    <form class="form-vertical" method="POST" action="{{ route('membro.exclusao_transferencia.store', ['id' => $pessoa->id]) }}" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-dark border-0 mb-4" role="alert">
            <strong>
              ATENÇÃO!!! ESTA AÇÃO NÃO PODE SER REVERTIDA.<br>
              Após transferir este membro para outra igreja, o mesmo será excluído desta igreja e não será listado no rol atual, apenas no rol permanente.<br>
              A igreja de destino será notificada da transferência para dar prosseguimento no processo de recepção.
            </strong>
          </div>
        </div>
      </div>

      <div class="form-group row mb-4">
        <div class="col-lg-2 text-right">
          <label class="control-label">* Data:</label>
        </div>
        <div class="col-lg-6">
          <input type="date" class="form-control @error('dt_notificacao') is-invalid @enderror" id="dt_notificacao" name="dt_notificacao" value="{{ old('dt_notificacao', date('Y-m-d')) }}" placeholder="ex: 31/12/2000">
          @error('dt_notificacao')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-group row mb-4">
        <div class="col-lg-2 text-right">
          <label class="control-label">Igreja de Destino:</label>
        </div>
        <div class="col-lg-6">
          <select id="igreja_id" name="igreja_id" class="form-control @error('igreja_id') is-invalid @enderror">
            <option value="" {{ old('igreja_id') == '' ? 'selected' : '' }}>Selecione</option>
            @foreach ($igrejas as $igreja)
            <option value="{{ $igreja->id }}" {{ old('igreja_id') == $igreja->id ? 'selected' : '' }}>{{ $igreja->instituicaoPai->instituicaoPai->nome ?? 'Sem Região' }} - {{ $igreja->instituicaoPai->nome ?? 'Sem distrito' }} - {{ $igreja->nome }}</option>
            @endforeach
          </select>
          @error('igreja_id')
          <div class="invalid-feedback">{{ $message }}</div>''
          @enderror
        </div>
      </div>

      <div class="form-group mt-4">
        <a href="{{ route('membro.editar', ['id' => $pessoa->id]) }}" class="btn btn-secondary">
          <x-bx-arrow-back /> Voltar
        </a>
        <button type="submit" class="btn btn-primary">
          <x-bx-transfer-alt /> Transferir
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('extras-scripts')
<script src="{{ asset('membros/js/editar.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $.fn.select2.defaults.set("language", "pt-BR");
    $('#igreja_id').select2({
      placeholder: 'Selecione',
      allowClear: true
    });
  });
</script>
@endsection
