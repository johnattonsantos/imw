@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'GCEU', 'url' => '/gceu/lista', 'active' => false],
    ['text' => 'Relatório Reunião', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@include('extras.alerts')
<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
          <div class="col-xl-12 col-md-12 col-sm-12 col-12">
              <h4>{{ $titulo }} - {{ $igreja }}</h4>
          </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" method="GET">
        <div class="form-group row mb-4">
          <div class="col-lg-3">
            <label class="control-label">GCEU</label>
            <select id="gceu_id" name="gceu_id" class="form-control">
              <option value="">Todos</option>
              @foreach ($gceus as $gceu)
                <option value="{{ $gceu->id }}" {{ request('gceu_id') == $gceu->id ? 'selected' : '' }}>{{ $gceu->nome }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-2">
            <label class="control-label">Tipo</label>
            <select id="tipo" name="tipo" class="form-control">
              <option value="">Todos</option>
              <option value="V" {{ request('tipo') === 'V' ? 'selected' : '' }}>Visitante</option>
              <option value="C" {{ request('tipo') === 'C' ? 'selected' : '' }}>Congregado</option>
              <option value="N" {{ request('tipo') === 'N' ? 'selected' : '' }}>Novo Convertido</option>
            </select>
          </div>
          <div class="col-lg-2">
            <label class="control-label">Data Inicial</label>
            <input type="date" name="data_inicial" class="form-control" value="{{ request('data_inicial') }}">
          </div>
          <div class="col-lg-2">
            <label class="control-label">Data Final</label>
            <input type="date" name="data_final" class="form-control" value="{{ request('data_final') }}">
          </div>
          <div class="col-lg-3">
            <button id="btn_buscar" type="submit" class="btn btn-primary" style="margin-top: 30px;">
              <x-bx-search /> Buscar
            </button>
          </div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="relatorio-reuniao-pessoas">
          <thead>
            <tr>
              <th>Nº</th>
              <th>IGREJA</th>
              <th>GCEU</th>
              <th>Nome</th>
              <th>Contato</th>
              <th>Tipo</th>
              <th>Data Reunião</th>
              <th>Data Cadastro</th>
              <th>Origem</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dados as $index => $item)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->instituicao_nome ?? '-' }}</td>
                <td>{{ $item->gceu_nome }}</td>
                <td>{{ $item->nome }}</td>
                <td>{{ $item->contato ?: '-' }}</td>
                <td>{{ $item->tipo }}</td>
                <td>{{ !empty($item->data_reuniao) ? \Carbon\Carbon::parse($item->data_reuniao)->format('d/m/Y') : '-' }}</td>
                <td>{{ !empty($item->data_cadastro) ? \Carbon\Carbon::parse($item->data_cadastro)->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $item->origem }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('extras-scripts')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
<script>
  new DataTable('#relatorio-reuniao-pessoas', {
    layout: {
      topStart: {
        buttons: [
          'pageLength',
          {
            extend: 'excel',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-excel"></i> Excel',
            title: "{{ $titulo }} - {{ $igreja }}"
          },
          {
            extend: 'pdf',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            pageSize: 'LETTER',
            orientation: 'landscape',
            title: "{{ $titulo }} - {{ $igreja }}"
          }
        ]
      },
      topEnd: 'search',
      bottomStart: 'info',
      bottomEnd: 'paging'
    },
    language: {
      emptyTable: 'Nenhum registro encontrado',
      info: 'Mostrando _START_ até _END_ de _TOTAL_ registros',
      infoEmpty: 'Mostrando 0 até 0 de 0 registros',
      lengthMenu: 'Mostrar _MENU_ registros',
      loadingRecords: 'Carregando...',
      processing: 'Processando...',
      search: 'Pesquisar:',
      zeroRecords: 'Nenhum registro encontrado',
      paginate: {
        first: 'Primeira',
        last: 'Última',
        next: 'Próxima',
        previous: 'Anterior'
      }
    }
  });
</script>
@endsection
