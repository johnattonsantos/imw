@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios', 'url' => '#', 'active' => false],
    ['text' => 'Cônjuges dos Clérigos', 'url' => '#', 'active' => true]
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
                    <h4>Cônjuges dos Clérigos</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4" id="relatorio-esposas-pastores">
                    <thead>
                        <tr>
                            <th>Clérigo</th>
                            <th>Cônjuge</th>
                            <th>Data de Nascimento</th>
                            <th>Telefone</th>
                            <th>Distrito</th>
                            <th>Igreja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($esposas as $item)
                            <tr>
                                <td>{{ $item->pastor_nome }}</td>
                                <td>{{ $item->esposa_nome }}</td>
                                <td>{{ $item->esposa_data_nascimento ? \Carbon\Carbon::parse($item->esposa_data_nascimento)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->pastor_telefone ? formatarTelefone($item->pastor_telefone) : '-' }}</td>
                                <td>{{ $item->distrito_nome ?? '-' }}</td>
                                <td>{{ $item->igreja_nome ?? '-' }}</td>
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
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>
<script>
    new DataTable('#relatorio-esposas-pastores', {
        layout: {
            topStart: {
                buttons: [
                    'pageLength',
                    {
                        extend: 'excel',
                        className: 'btn btn-primary btn-rounded',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        titleAttr: 'Excel',
                        title: "RELATÓRIO CÔNJUGES DOS CLÉRIGOS - {{ session()->get('session_perfil')->instituicao_nome }}"
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary btn-rounded',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        titleAttr: 'PDF',
                        title: "RELATÓRIO CÔNJUGES DOS CLÉRIGOS - {{ session()->get('session_perfil')->instituicao_nome }}"
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-primary btn-rounded',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        titleAttr: 'Imprimir',
                        title: "RELATÓRIO CÔNJUGES DOS CLÉRIGOS - {{ session()->get('session_perfil')->instituicao_nome }}"
                    }
                ]
            }
        },
        pageLength: 25,
        order: [[0, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json',
            emptyTable: 'Nenhum registro encontrado.'
        }
    });
</script>
@endsection
