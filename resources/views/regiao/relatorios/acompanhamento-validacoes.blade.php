@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'Validação de Membros', 'url' => route('regiao.relatorio.acompanhamento-validacoes'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<style>
  .validacao-percentual {
    font-weight: 700;
  }

  .validacao-progress {
    background: #e9edf3;
    border-radius: 10px;
    height: 6px;
    margin-top: 5px;
    overflow: hidden;
    width: 100%;
  }

  .validacao-progress span {
    background: #27865d;
    display: block;
    height: 100%;
  }
</style>
@endsection

@include('extras.alerts')

@section('content')
@php
  $tituloRelatorio = 'VALIDAÇÃO DE MEMBROS - ' . $regiao->nome;
@endphp

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-12">
          <h4>Validação de Membros</h4>
          <p class="pl-3 mb-0">Região: {{ $regiao->nome }}</p>
          <p class="pl-3">Igrejas encontradas: {{ $igrejas->count() }}</p>
        </div>
      </div>
    </div>

    <div class="widget-content widget-content-area">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="acompanhamento-validacoes-table" style="width: 100%;">
          <thead>
            <tr>
              <th>REGIÃO</th>
              <th>DISTRITO</th>
              <th>IGREJA</th>
              <th>VALIDADAS</th>
              <th>PENDENTES</th>
              <th>TOTAL</th>
              <th>%</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($igrejas as $igreja)
              <tr>
                <td>{{ $igreja->regiao }}</td>
                <td>{{ $igreja->distrito }}</td>
                <td>{{ $igreja->igreja }}</td>
                <td>{{ number_format($igreja->validadas, 0, ',', '.') }}</td>
                <td>{{ number_format($igreja->pendentes, 0, ',', '.') }}</td>
                <td>{{ number_format($igreja->total, 0, ',', '.') }}</td>
                <td data-order="{{ $igreja->percentual }}">
                  <span class="validacao-percentual">{{ number_format($igreja->percentual, 2, ',', '.') }}%</span>
                  <div class="validacao-progress" aria-hidden="true">
                    <span style="width: {{ min($igreja->percentual, 100) }}%;"></span>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3">TOTAL DA REGIÃO</th>
              <th>{{ number_format($resumo->validadas, 0, ',', '.') }}</th>
              <th>{{ number_format($resumo->pendentes, 0, ',', '.') }}</th>
              <th>{{ number_format($resumo->total, 0, ',', '.') }}</th>
              <th>{{ number_format($resumo->percentual, 2, ',', '.') }}%</th>
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

  new DataTable('#acompanhamento-validacoes-table', {
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
            pageSize: 'A4',
            customize: function (doc) {
              const tableNode = doc.content.find(function (item) {
                return item.table;
              });

              if (tableNode) {
                tableNode.table.widths = ['13%', '20%', '27%', '10%', '10%', '10%', '10%'];
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
      emptyTable: 'Nenhuma igreja encontrada',
      info: 'Mostrando de _START_ até _END_ de _TOTAL_ igrejas',
      infoEmpty: 'Mostrando 0 até 0 de 0 igrejas',
      infoFiltered: '(Filtradas de _MAX_ igrejas)',
      lengthMenu: '_MENU_ resultados por página',
      loadingRecords: 'Carregando...',
      processing: 'Processando...',
      search: 'Pesquisar',
      zeroRecords: 'Nenhuma igreja encontrada',
      paginate: {first: 'Primeiro', last: 'Último', next: 'Próximo', previous: 'Anterior'}
    }
  });
</script>
@endsection
