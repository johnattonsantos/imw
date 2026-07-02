@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios', 'url' => '#', 'active' => false],
    ['text' => 'Relatório de Família', 'url' => route('relatorio.familia'), 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<style>
  #relatorio-familia-table td {
    white-space: normal;
    vertical-align: top;
  }

  #relatorio-familia-table .lista-filhos {
    margin: 0;
    padding-left: 18px;
  }
</style>
@endsection

@include('extras.alerts')

@section('content')
@php
  $igrejaNome = session()->get('session_perfil')->instituicoes->igrejaLocal->nome;
  $tituloRelatorio = 'RELATÓRIO DE FAMÍLIA - ' . $igrejaNome;
@endphp

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-12">
          <h4>Relatório de Família</h4>
          <p class="pl-3 mb-0">Igreja Local: {{ $igrejaNome }}</p>
          <p class="pl-3">Registros encontrados: {{ $membros->count() }}</p>
        </div>
      </div>
    </div>

    <div class="widget-content widget-content-area">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 display" id="relatorio-familia-table" style="width: 100%;">
          <thead>
            <tr>
              <th>NOME DO MEMBRO</th>
              <th>MÃE</th>
              <th>PAI</th>
              <th>CÔNJUGE</th>
              <th>DATA DO CASAMENTO</th>
              <th>FILHOS</th>
              <th>HISTÓRICO FAMILIAR</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($membros as $membro)
              @php
                $familiar = $membro->familiar;
                $filhos = array_values(array_filter(
                  preg_split('/\s*;\s*/', (string) optional($familiar)->filhos),
                  fn ($filho) => trim($filho) !== ''
                ));
              @endphp
              <tr>
                <td>{{ $membro->nome }}</td>
                <td>{{ optional($familiar)->mae_nome ?: '-' }}</td>
                <td>{{ optional($familiar)->pai_nome ?: '-' }}</td>
                <td>{{ optional($familiar)->conjuge_nome ?: '-' }}</td>
                <td>{{ optional($familiar)->data_casamento ? \Carbon\Carbon::parse($familiar->data_casamento)->format('d/m/Y') : '-' }}</td>
                <td>
                  @if ($filhos)
                    <ul class="lista-filhos">
                      @foreach ($filhos as $filho)
                        <li>{{ trim($filho) }}</li>
                      @endforeach
                    </ul>
                  @else
                    -
                  @endif
                </td>
                <td>{{ optional($familiar)->historico_familiar ?: '-' }}</td>
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
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
<script>
  const reportTitle = @json($tituloRelatorio);
  const exportFormat = {
    body: function (data, row, column, node) {
      const items = $(node).find('li').map(function () {
        return $(this).text().trim();
      }).get();

      return items.length ? items.join('\n') : $(node).text().trim();
    }
  };

  new DataTable('#relatorio-familia-table', {
    pageLength: 10,
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
            title: reportTitle,
            exportOptions: {format: exportFormat}
          },
          {
            extend: 'pdf',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            titleAttr: 'PDF',
            title: reportTitle,
            orientation: 'landscape',
            pageSize: 'A4',
            exportOptions: {format: exportFormat},
            customize: function (doc) {
              const tableNode = doc.content.find(function (item) {
                return item.table;
              });

              doc.defaultStyle.fontSize = 8;
              if (tableNode) {
                tableNode.table.widths = ['16%', '14%', '14%', '14%', '11%', '15%', '16%'];
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
      emptyTable: 'Nenhum registro encontrado',
      info: 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
      infoEmpty: 'Mostrando 0 até 0 de 0 registros',
      infoFiltered: '(Filtrados de _MAX_ registros)',
      lengthMenu: '_MENU_ resultados por página',
      loadingRecords: 'Carregando...',
      processing: 'Processando...',
      search: 'Pesquisar',
      zeroRecords: 'Nenhum registro encontrado',
      paginate: {first: 'Primeiro', last: 'Último', next: 'Próximo', previous: 'Anterior'}
    }
  });
</script>
@endsection
