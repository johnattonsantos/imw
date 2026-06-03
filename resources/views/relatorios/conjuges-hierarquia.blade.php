@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => $breadcrumbGrupo, 'url' => '#', 'active' => false],
    ['text' => 'Cônjuges', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
  <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
      <div class="widget-header">
        <div class="row">
            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                <h4>Cônjuges</h4>
                <p class="pl-3 mb-0">{{ $nivel === 'regiao' ? 'Região' : 'Distrito' }}: {{ $instituicaoNome }}</p>
                <p class="pl-3">Registros Encontrados: {{ $membros->count() }}</p>
            </div>
        </div>
      </div>
  </div>
</div>

@if ($nivel === 'regiao')
<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
          <h4>Total por Distrito</h4>
        </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4" id="conjuges-total-distrito-table">
          <thead>
            <tr>
              <th>DISTRITO</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($totaisDistritos as $totalDistrito)
              <tr>
                <td>{{ $totalDistrito->distrito_nome }}</td>
                <td>{{ $totalDistrito->total }}</td>
              </tr>
            @empty
              <tr>
                <td>Nenhum registro encontrado</td>
                <td>0</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endif

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
          <h4>Total por Igreja</h4>
        </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4" id="conjuges-total-igreja-table">
          <thead>
            <tr>
              @if ($nivel === 'regiao')
                <th>DISTRITO</th>
              @endif
              <th>IGREJA</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($totaisIgrejas as $totalIgreja)
              <tr>
                @if ($nivel === 'regiao')
                  <td>{{ $totalIgreja->distrito_nome }}</td>
                @endif
                <td>{{ $totalIgreja->igreja_nome }}</td>
                <td>{{ $totalIgreja->total }}</td>
              </tr>
            @empty
              <tr>
                @if ($nivel === 'regiao')
                  <td>Nenhum registro encontrado</td>
                @endif
                <td>{{ $nivel === 'regiao' ? '-' : 'Nenhum registro encontrado' }}</td>
                <td>0</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
          <h4>Detalhamento</h4>
        </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="conjuges-detalhe-table">
          <thead>
            <tr>
              @if ($nivel === 'regiao')
                <th>DISTRITO</th>
              @endif
              <th>IGREJA</th>
              <th>NOME DO MEMBRO</th>
              <th>NOME DO CÔNJUGE</th>
              <th>DATA DO CASAMENTO</th>
              <th>CONTATO</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($membros as $membro)
              <tr>
                @if ($nivel === 'regiao')
                  <td>{{ $membro->distrito_nome }}</td>
                @endif
                <td>{{ $membro->igreja_nome }}</td>
                <td>{{ $membro->membro_nome }}</td>
                <td>{{ $membro->conjuge_nome }}</td>
                <td>{{ $membro->data_casamento ? \Carbon\Carbon::parse($membro->data_casamento)->format('d/m/Y') : '-' }}</td>
                <td>{{ $membro->contato ? formatStr($membro->contato, '## (##) #####-####') : '-' }}</td>
              </tr>
            @empty
              <tr>
                @if ($nivel === 'regiao')
                  <td>Nenhum registro encontrado</td>
                @endif
                <td>{{ $nivel === 'regiao' ? '-' : 'Nenhum registro encontrado' }}</td>
                <td>-</td>
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
  const reportTitle = @json($titulo);
  const language = {
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
    }
  };

  function exportLayout(title) {
    return {
      topStart: {
        buttons: [
          'pageLength',
          {
            extend: 'excel',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-excel"></i> Excel',
            titleAttr: 'Excel',
            title: title
          },
          {
            extend: 'pdf',
            className: 'btn btn-primary btn-rounded',
            text: '<i class="fas fa-file-pdf"></i> PDF',
            titleAttr: 'PDF',
            title: title,
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
    };
  }

  @if ($nivel === 'regiao')
    new DataTable('#conjuges-total-distrito-table', {
      layout: exportLayout(reportTitle + ' - TOTAL POR DISTRITO'),
      language: language
    });
  @endif

  new DataTable('#conjuges-total-igreja-table', {
    layout: exportLayout(reportTitle + ' - TOTAL POR IGREJA'),
    language: language
  });

  new DataTable('#conjuges-detalhe-table', {
    layout: exportLayout(reportTitle + ' - DETALHAMENTO'),
    language: language
  });
</script>
@endsection
