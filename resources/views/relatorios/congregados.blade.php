@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => $breadcrumbGrupo, 'url' => '#', 'active' => false],
    ['text' => 'Congregados', 'url' => '#', 'active' => true]
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
          <h4>{{ __('Congregados Ativos') }}</h4>
        </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="relatorio-congregados-table">
          <thead>
            <tr>
              @if ($nivel === 'regiao')
                <th>{{ __('DISTRITO') }}</th>
              @endif
              @if ($nivel !== 'local')
                <th>{{ __('IGREJA') }}</th>
              @endif
              <th>{{ __('CONGREGAÇÃO/SEDE') }}</th>
              <th>{{ __('NOME DO CONGREGADO') }}</th>
              <th>{{ __('CONTATO') }}</th>
              <th>{{ __('BAIRRO') }}</th>
              <th>{{ __('DATA DE CADASTRO') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($congregados as $congregado)
              <tr>
                @if ($nivel === 'regiao')
                  <td>{{ $congregado->distrito_nome ?: '-' }}</td>
                @endif
                @if ($nivel !== 'local')
                  <td>{{ $congregado->igreja_nome ?: '-' }}</td>
                @endif
                <td>{{ $congregado->localidade_nome }}</td>
                <td>{{ $congregado->congregado_nome }}</td>
                <td>{{ $congregado->contato ? formatStr($congregado->contato, '## (##) #####-####') : '-' }}</td>
                <td>{{ $congregado->bairro ?: '-' }}</td>
                <td>{{ $congregado->data_cadastro ? \Carbon\Carbon::parse($congregado->data_cadastro)->format('d/m/Y') : '-' }}</td>
              </tr>
            @empty
              <tr>
                @if ($nivel === 'regiao')
                  <td>{{ __('Nenhum registro encontrado') }}</td>
                @endif
                @if ($nivel !== 'local')
                  <td>{{ $nivel === 'regiao' ? '-' : __('Nenhum registro encontrado') }}</td>
                @endif
                <td>{{ $nivel === 'local' ? __('Nenhum registro encontrado') : '-' }}</td>
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

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
          <h4>{{ __('Total por Igreja') }}</h4>
        </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4">
          <thead>
            <tr>
              @if ($nivel === 'regiao')
                <th>{{ __('DISTRITO') }}</th>
              @endif
              <th>{{ __('IGREJA') }}</th>
              <th>{{ __('TOTAL DE CONGREGADOS') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($totaisPorIgreja as $totalIgreja)
              <tr>
                @if ($nivel === 'regiao')
                  <td>{{ $totalIgreja->distrito_nome ?: '-' }}</td>
                @endif
                <td>{{ $totalIgreja->igreja_nome ?: '-' }}</td>
                <td>{{ $totalIgreja->total }}</td>
              </tr>
            @empty
              <tr>
                @if ($nivel === 'regiao')
                  <td>{{ __('Nenhum registro encontrado') }}</td>
                @endif
                <td>{{ $nivel === 'regiao' ? '-' : __('Nenhum registro encontrado') }}</td>
                <td>0</td>
              </tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr>
              @if ($nivel === 'regiao')
                <th colspan="2">{{ __('Total Geral') }}</th>
              @else
                <th>{{ __('Total Geral') }}</th>
              @endif
              <th>{{ $totalGeral }}</th>
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
  const reportTitle = @json($titulo);
  const language = {
    decimal: ",",
    thousands: ".",
    emptyTable: @json(__('Nenhum registro encontrado')),
    info: @json(__('Mostrando de _START_ até _END_ de _TOTAL_ registros')),
    infoEmpty: @json(__('Mostrando 0 até 0 de 0 registros')),
    infoFiltered: @json(__('(Filtrados de _MAX_ registros)')),
    lengthMenu: @json(__('_MENU_ resultados por página')),
    loadingRecords: @json(__('Carregando...')),
    processing: @json(__('Processando...')),
    search: @json(__('Pesquisar')),
    zeroRecords: @json(__('Nenhum registro encontrado')),
    paginate: {
      first: @json(__('Primeiro')),
      last: @json(__('Último')),
      next: @json(__('Próximo')),
      previous: @json(__('Anterior'))
    }
  };

  new DataTable('#relatorio-congregados-table', {
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
    language: language
  });
</script>
@endsection
