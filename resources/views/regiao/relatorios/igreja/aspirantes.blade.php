@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Igrejas', 'url' => '#', 'active' => false],
        ['text' => 'Aspirantes por Igrejas', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
  <div class="statbox widget box box-shadow">
    <div class="widget-header">
      <div class="row">
          <div class="col-xl-12 col-md-12 col-sm-12 col-12">
              <h4>Relatório Aspirantes por Igrejas</h4>
          </div>
      </div>
    </div>
    <div class="widget-content widget-content-area">
      <form class="form-vertical" id="filter_form" method="GET">
        <input type="hidden" name="buscar" value="todos">
        <div class="form-group row mb-4">
          <div class="col-lg-2 text-right">
            <label class="control-label">Distrito:</label>
          </div>
          <div class="col-lg-4">
            <select class="form-control" name="distrito_id">
              <option value="all">Todos</option>
              @foreach ($distritos as $distrito)
                <option value="{{ $distrito->id }}" {{ request()->input('distrito_id', 'all') == $distrito->id ? 'selected' : '' }}>
                  {{ $distrito->nome }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-2 text-right">
            <label class="control-label">Igreja:</label>
          </div>
          <div class="col-lg-4">
            <select class="form-control" name="igreja_id">
              <option value="all">Todas</option>
              @foreach ($igrejas as $igreja)
                <option value="{{ $igreja->id }}" {{ request()->input('igreja_id', 'all') == $igreja->id ? 'selected' : '' }}>
                  {{ $igreja->nome }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row mb-4">
          <div class="col-lg-2"></div>
          <div class="col-lg-6">
            <button id="btn_buscar" type="submit" name="action" value="buscar" class="btn btn-primary btn">
              <x-bx-search /> Buscar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@if (request()->has('buscar'))
  <div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
      <div class="widget-content widget-content-area">
        <div class="table-responsive mt-0">
          <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="aspirantes-regiao">
            <thead>
                <tr>
                    <th>DISTRITO</th>
                    <th>IGREJA</th>
                    <th>NOME</th>
                    <th>SEXO</th>
                    <th>ESTADO CIVIL</th>
                    <th>CPF</th>
                    <th>NASCIMENTO</th>
                    <th>CONTATO</th>
                    <th>E-MAIL</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($aspirantes as $membro)
                  <tr>
                      <td>{{ $membro->distrito_nome }}</td>
                      <td>{{ $membro->igreja_nome }}</td>
                      <td>{{ $membro->membro_nome }}</td>
                      <td>{{ $membro->sexo }}</td>
                      <td>{{ $membro->estado_civil }}</td>
                      <td>{{ formatStr($membro->cpf, '###.###.###-##') }}</td>
                      <td>{{ $membro->data_nascimento }}</td>
                      <td>{{ formatStr($membro->contato, '## (##) #####-####') }}</td>
                      <td>{{ $membro->email }}</td>
                  </tr>
                @empty
                @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endif
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
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>
    <script>
    @if (request()->has('buscar'))
    new DataTable('#aspirantes-regiao', {
        scrollX: true,
        scrollY: 400,
        scrollCollapse: true,
        layout: {
            topStart: {
                buttons: [
                    'pageLength',
                    {
                        extend: 'excel',
                        className: 'btn btn-primary btn-rounded',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        title: 'IMW - RELATÓRIO ASPIRANTES POR IGREJAS (REGIONAL)'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary btn-rounded',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        title: 'IMW - RELATÓRIO ASPIRANTES POR IGREJAS (REGIONAL)',
                        orientation: 'landscape'
                    }
                ]
            },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json',
            emptyTable: 'Nenhum resultado encontrado.'
        }
    });
    @endif
    </script>
@endsection
