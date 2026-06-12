@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios', 'url' => '#', 'active' => false],
    ['text' => 'Membros por Bairro', 'url' => '#', 'active' => true]
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
  $localidade = $localidade ?? request()->get('localidade', 'todos');
  $localidadeTexto = $localidadeTexto ?? 'Todos';
  $tituloRelatorio = 'RELATÓRIO DE MEMBROS POR BAIRRO - ' . $igrejaNome . ' - ' . strtoupper($localidadeTexto);
@endphp

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
          <h4>Membros por Bairro</h4>
          <p class="pl-3 mb-0">Igreja Local: {{ $igrejaNome }}</p>
          <p class="pl-3 mb-0">Filtro: {{ $localidadeTexto }}</p>
          <p class="pl-3">Registros Encontrados: {{ $membros->count() }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
          <h4>Filtros</h4>
        </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <form method="GET" action="{{ route('relatorio.membros-por-bairro') }}">
        <div class="row align-items-end">
          <div class="col-md-4 form-group">
            <label for="localidade">Congregação/Sede</label>
            <select id="localidade" name="localidade" class="form-control">
              <option value="todos" {{ $localidade === 'todos' ? 'selected' : '' }}>Todos</option>
              <option value="sede" {{ $localidade === 'sede' ? 'selected' : '' }}>Sede</option>
              <option value="congregacao" {{ $localidade === 'congregacao' ? 'selected' : '' }}>Congregação</option>
            </select>
          </div>
          <div class="col-md-4 form-group">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('relatorio.membros-por-bairro') }}" class="btn btn-secondary">Limpar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
          <h4>Membros por Bairro</h4>
        </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="membros-bairro-detalhe-table">
          <thead>
            <tr>
              <th>NOME DO MEMBRO</th>
              <th>CONGREGAÇÃO/SEDE</th>
              <th>BAIRRO</th>
              <th>CEP</th>
              <th>ENDEREÇO</th>
              <th>CIDADE/UF</th>
              <th>CONTATO</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($membros as $membro)
              <tr>
                <td>{{ $membro->nome }}</td>
                <td>{{ $membro->localidade_nome }}</td>
                <td>{{ $membro->bairro }}</td>
                <td>{{ $membro->cep ? formatStr($membro->cep, '#####-###') : '-' }}</td>
                <td>
                  {{ $membro->endereco ?: '-' }}
                  @if ($membro->numero)
                    , {{ $membro->numero }}
                  @endif
                  @if ($membro->complemento)
                    - {{ $membro->complemento }}
                  @endif
                </td>
                <td>{{ $membro->cidade ? $membro->cidade . '/' . $membro->estado : '-' }}</td>
                <td>{{ $membro->contato ? formatStr($membro->contato, '## (##) #####-####') : '-' }}</td>
              </tr>
            @empty
              <tr>
                <td>Nenhum registro encontrado</td>
                <td>-</td>
                <td>-</td>
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
  const reportTitle = @json($tituloRelatorio);
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

  new DataTable('#membros-bairro-detalhe-table', {
    layout: exportLayout(reportTitle + ' - DETALHAMENTO'),
    language: language
  });
</script>
@endsection
