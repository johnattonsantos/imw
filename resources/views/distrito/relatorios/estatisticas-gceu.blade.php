@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Distritais', 'url' => '#', 'active' => false],
    ['text' => 'Estatísticas GCEU', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Relatório Estatísticas GCEU</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table class="table table-bordered table-striped display nowrap" id="estatisticas-gceu-distrito" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Igreja</th>
                            <th>Quant. GCEUS</th>
                            <th>% GCEUS</th>
                            <th>Quant. Membros</th>
                            <th>% Membros</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalGceus = (int) ($totais['qtd_gceus'] ?? 0);
                            $totalMembros = (int) ($totais['qtd_membros_gceu'] ?? 0);
                        @endphp
                        @foreach($dados as $item)
                            <tr>
                                <td>{{ $item->igreja }}</td>
                                <td>{{ (int) $item->qtd_gceus }}</td>
                                <td>
                                    {{ $totalGceus > 0 ? number_format((((int) $item->qtd_gceus) / $totalGceus) * 100, 2, ',', '.') : '0,00' }}%
                                </td>
                                <td>{{ (int) $item->qtd_membros_gceu }}</td>
                                <td>
                                    {{ $totalMembros > 0 ? number_format((((int) $item->qtd_membros_gceu) / $totalMembros) * 100, 2, ',', '.') : '0,00' }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: right;">TOTAL GERAL:</th>
                            <th>{{ $totais['qtd_gceus'] ?? 0 }}</th>
                            <th>100,00%</th>
                            <th>{{ $totais['qtd_membros_gceu'] ?? 0 }}</th>
                            <th>100,00%</th>
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
        new DataTable('#estatisticas-gceu-distrito', {
            order: [[1, 'desc']],
            pageLength: 25,
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: 'IMW - RELATÓRIO ESTATÍSTICAS GCEU (DISTRITAL)'
                        },
                        {
                            extend: 'pdf',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            title: 'IMW - RELATÓRIO ESTATÍSTICAS GCEU (DISTRITAL)',
                            customize: function (doc) {
                                if (doc.content && doc.content[1] && doc.content[1].table) {
                                    const columnCount = doc.content[1].table.body[0].length;
                                    doc.content[1].table.widths = Array(columnCount).fill('*');
                                    const centeredColumns = [1, 2, 3, 4];
                                    doc.content[1].table.body.forEach(function (row) {
                                        centeredColumns.forEach(function (index) {
                                            if (row[index] !== undefined) {
                                                if (typeof row[index] === 'string' || typeof row[index] === 'number') {
                                                    row[index] = { text: String(row[index]), alignment: 'center' };
                                                } else {
                                                    row[index].alignment = 'center';
                                                }
                                            }
                                        });
                                    });
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
                processing: 'Processando...',
                loadingRecords: 'Carregando...',
                lengthMenu: 'Exibir _MENU_ resultados por página',
                zeroRecords: 'Nenhum registro encontrado',
                emptyTable: 'Nenhum registro encontrado',
                info: 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
                infoEmpty: 'Mostrando 0 até 0 de 0 registros',
                infoFiltered: '(filtrado de _MAX_ registros no total)',
                search: 'Pesquisar',
                paginate: {
                    first: 'Primeira',
                    previous: 'Anterior',
                    next: 'Próxima',
                    last: 'Última'
                },
                aria: {
                    sortAscending: ': ativar para ordenar a coluna de forma crescente',
                    sortDescending: ': ativar para ordenar a coluna de forma decrescente'
                }
            }
        });
    </script>
@endsection
