@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'Perfil dos Membros Excluídos', 'url' => route('regiao.relatorio.perfil-membros-excluidos'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
@php
  $periodoInicial = \Carbon\Carbon::parse($dataInicial)->format('d/m/Y');
  $periodoFinal = \Carbon\Carbon::parse($dataFinal)->format('d/m/Y');
  $tituloRelatorio = 'PERFIL DOS MEMBROS EXCLUÍDOS - ' . $regiao->nome . ' - ' . $periodoInicial . ' A ' . $periodoFinal;
@endphp

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-12">
          <h4>Perfil dos Membros Excluídos</h4>
          <p class="pl-3 mb-0">Região: {{ $regiao->nome }}</p>
          <p class="pl-3">Registros encontrados: {{ $membros->count() }}</p>
        </div>
      </div>
    </div>

    <div class="widget-content widget-content-area">
      <form method="GET" class="mb-4">
        <div class="row align-items-end">
          <div class="col-md-3 col-lg-2 mb-2">
            <label for="data_inicial">Data Inicial</label>
            <input type="date" name="data_inicial" id="data_inicial" class="form-control form-control-sm @error('data_inicial') is-invalid @enderror" value="{{ $dataInicial }}" required>
          </div>
          <div class="col-md-3 col-lg-2 mb-2">
            <label for="data_final">Data Final</label>
            <input type="date" name="data_final" id="data_final" class="form-control form-control-sm @error('data_final') is-invalid @enderror" value="{{ $dataFinal }}" required>
          </div>
          <div class="col-md-3 col-lg-2 mb-2">
            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
          </div>
        </div>
        @error('data_final')
          <span class="text-danger">{{ $message }}</span>
        @enderror
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="perfil-membros-excluidos-table" style="width: 100%;">
          <thead>
            <tr>
              <th>DISTRITO</th>
              <th>IGREJA</th>
              <th>NOME DO MEMBRO EXCLUÍDO</th>
              <th>IDADE</th>
              <th>MODO DA EXCLUSÃO</th>
              <th>ESCOLARIDADE</th>
              <th>ESTADO CIVIL</th>
              <th>MINISTÉRIO</th>
              <th>FUNÇÃO ECLESIÁSTICA</th>
              <th>DATA DA EXCLUSÃO</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($membros as $membro)
              <tr>
                <td>{{ $membro->distrito }}</td>
                <td>{{ $membro->igreja }}</td>
                <td>{{ $membro->membro }}</td>
                <td>{{ $membro->idade !== null ? $membro->idade : '-' }}</td>
                <td>{{ $membro->modo_exclusao ?: 'Não informado' }}</td>
                <td>{{ $membro->escolaridade ?: 'Não informado' }}</td>
                <td>{{ $membro->estado_civil }}</td>
                <td>{{ $membro->ministerio }}</td>
                <td>{{ $membro->funcao_eclesiastica ?: 'Não informado' }}</td>
                <td>{{ \Carbon\Carbon::parse($membro->data_exclusao)->format('d/m/Y') }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2">TOTAL DA REGIÃO</th>
              <th>{{ number_format($membros->count(), 0, ',', '.') }}</th>
              <th colspan="7"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('extras-scripts')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
<script>
  const reportTitle = @json($tituloRelatorio);

  new DataTable('#perfil-membros-excluidos-table', {
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
    layout: {
      topStart: {
        buttons: [
          'pageLength',
          {
            extend: 'excel',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-excel"></i> Excel',
            titleAttr: 'Excel',
            title: reportTitle
          },
          {
            extend: 'pdf',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            titleAttr: 'PDF',
            title: reportTitle,
            orientation: 'landscape',
            pageSize: 'A3',
            customize: function (doc) {
              const tableNode = doc.content.find(function (item) {
                return item.table;
              });

              doc.defaultStyle.fontSize = 7;
              doc.pageMargins = [18, 28, 18, 28];
              if (tableNode) {
                tableNode.table.widths = ['12%', '12%', '15%', '5%', '10%', '11%', '8%', '8%', '12%', '7%'];
              }
            }
          }
        ]
      },
      topEnd: 'search',
      bottomStart: 'info',
      bottomEnd: 'paging'
    },
    language: {
      decimal: ',',
      thousands: '.',
      emptyTable: 'Nenhum membro excluído no período',
      info: 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
      infoEmpty: 'Mostrando 0 até 0 de 0 registros',
      infoFiltered: '(Filtrados de _MAX_ registros)',
      lengthMenu: '_MENU_ resultados por página',
      loadingRecords: 'Carregando...',
      processing: 'Processando...',
      search: 'Pesquisar',
      zeroRecords: 'Nenhum membro excluído no período',
      paginate: {first: 'Primeiro', last: 'Último', next: 'Próximo', previous: 'Anterior'}
    }
  });
</script>
@endsection
