@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios', 'url' => '#', 'active' => false],
    ['text' => 'Relatório de Cônjuges', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
@php
  $igrejaNome = session()->get('session_perfil')->instituicoes->igrejaLocal->nome;
  $tituloRelatorio = 'RELATÓRIO DE CÔNJUGES - ' . $igrejaNome;
@endphp

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
      <div class="widget-header">
        <div class="row">
            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                <h4>Relatório de Cônjuges</h4>
                <p class="pl-3 mb-0">Igreja Local: {{ $igrejaNome }}</p>
                <p class="pl-3">Registros Encontrados: {{ $membros->count() }}</p>
            </div>
        </div>
      </div>
      <div class="widget-content widget-content-area">
          <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="relatorio-conjuges-table">
                  <thead>
                      <tr>
                          <th>NOME DO MEMBRO</th>
                          <th>NOME DO CÔNJUGE</th>
                          <th>DATA DO CASAMENTO</th>
                          <th>CONTATO</th>
                      </tr>
                  </thead>
                  <tbody>
                    @forelse ($membros as $membro)
                      <tr>
                          <td>{{ $membro->nome }}</td>
                          <td>{{ $membro->conjuge_nome }}</td>
                          <td>{{ $membro->data_casamento ? \Carbon\Carbon::parse($membro->data_casamento)->format('d/m/Y') : '-' }}</td>
                          <td>{{ $membro->contato ? formatStr($membro->contato, '## (##) #####-####') : '-' }}</td>
                      </tr>
                    @empty
                      <tr>
                          <td>Nenhum registro encontrado</td>
                          <td>-</td>
                          <td>-</td>
                          <td>-</td>
                      </tr>
                    @endforelse
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

  new DataTable('#relatorio-conjuges-table', {
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
            customize: function (doc) {
              const tableNode = doc.content.find(function (item) {
                return item.table;
              });

              if (tableNode) {
                const columns = tableNode.table.body[0].length;
                tableNode.table.widths = Array(columns).fill('*');
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
      decimal: ",",
      thousands: ".",
      emptyTable: "Nenhum registro encontrado",
      info: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "Mostrando 0 até 0 de 0 registros",
      infoFiltered: "(Filtrados de _MAX_ registros)",
      lengthMenu: "_MENU_ resultados por página",
      loadingRecords: "Carregando...",
      processing: "Processando...",
      search: "Pesquisar",
      zeroRecords: "Nenhum registro encontrado",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior"
      },
      aria: {
        sortAscending: ": Ordenar colunas de forma ascendente",
        sortDescending: ": Ordenar colunas de forma descendente"
      }
    }
  });
</script>
@endsection
