@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Relatório de Eventos', 'url' => route('eventos.relatorio'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<style>
    @media print {
        .no-print,
        .navbar,
        .sidebar-wrapper,
        .header-container,
        .breadcrumb-one,
        .footer-wrapper {
            display: none !important;
        }

        #content {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .statbox,
        .widget-content {
            box-shadow: none !important;
            border: 0 !important;
        }
    }
</style>
@endsection

@section('content')
@php
    $tituloRelatorio = 'RELATÓRIO DE EVENTOS';
@endphp

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Relatório de Eventos</h4>
                    <p class="pl-3">Registros Encontrados: {{ $eventos->count() }}</p>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-3 no-print">
                <div class="row align-items-center">
                    <div class="col-lg-2 mb-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Pesquisar evento, local ou descrição">
                    </div>
                    <div class="col-lg-3 mb-2">
                        <select name="instituicao_id" class="form-control form-control-sm">
                            <option value="">Todas as igrejas/congregações</option>
                            @foreach ($instituicoesEvento->groupBy('grupo') as $grupo => $instituicoesGrupo)
                                <optgroup label="{{ $grupo }}">
                                    @foreach ($instituicoesGrupo as $instituicaoEvento)
                                        <option value="{{ $instituicaoEvento->id }}" {{ (string) request('instituicao_id') === (string) $instituicaoEvento->id ? 'selected' : '' }}>
                                            {{ $instituicaoEvento->label }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 mb-2">
                        <select name="evento_proposito_id" class="form-control form-control-sm">
                            <option value="">Todos os propósitos</option>
                            @foreach ($propositos as $proposito)
                                <option value="{{ $proposito->id }}" {{ (string) request('evento_proposito_id') === (string) $proposito->id ? 'selected' : '' }}>{{ $proposito->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 mb-2">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Todos os status</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 mb-2">
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="form-control form-control-sm" title="A partir de">
                    </div>
                    <div class="col-lg-1 mb-2">
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="form-control form-control-sm" title="Até">
                    </div>
                    <div class="col-lg-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="eventos-relatorio-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>EVENTO</th>
                            @if ($escopoEvento === 'regiao')
                                <th>DISTRITO</th>
                            @endif
                            @if (in_array($escopoEvento, ['regiao', 'distrito'], true))
                                <th>IGREJA</th>
                            @endif
                            <th>SEDE/CONGREGAÇÃO</th>
                            <th>PROPÓSITO</th>
                            <th>AGENDA</th>
                            <th>LOCAL INFORMADO</th>
                            <th>LÍDER</th>
                            <th>STATUS</th>
                            <th class="no-export">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($eventos as $evento)
                            @php
                                $agenda = optional($evento->data_inicio)->format('d/m/Y');
                                if ($evento->hora_inicio) {
                                    $agenda .= ' ' . substr((string) $evento->hora_inicio, 0, 5);
                                }
                                if ($evento->data_fim) {
                                    $agenda .= ' até ' . optional($evento->data_fim)->format('d/m/Y');
                                    if ($evento->hora_fim) {
                                        $agenda .= ' ' . substr((string) $evento->hora_fim, 0, 5);
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $evento->titulo }}</td>
                                @if ($escopoEvento === 'regiao')
                                    <td>{{ $evento->evento_distrito_nome }}</td>
                                @endif
                                @if (in_array($escopoEvento, ['regiao', 'distrito'], true))
                                    <td>{{ $evento->evento_igreja_nome }}</td>
                                @endif
                                <td>{{ $evento->evento_local_nome }}</td>
                                <td>{{ optional($evento->proposito)->nome ?: '-' }}</td>
                                <td>{{ $agenda }}</td>
                                <td>{{ $evento->local ?: '-' }}</td>
                                <td>{{ optional($evento->lider)->nome ?: '-' }}</td>
                                <td>{{ $statusOptions[$evento->status] ?? $evento->status }}</td>
                                <td class="no-export text-center">
                                    <a href="{{ route('eventos.relatorio.evento-pdf', $evento) }}" target="_blank" class="btn btn-danger btn-sm btn-rounded" title="Gerar PDF">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </td>
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
    const language = {
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
        paginate: {
            first: 'Primeiro',
            last: 'Último',
            next: 'Próximo',
            previous: 'Anterior'
        },
        buttons: {
            pageLength: {
                '-1': 'Mostrar todos os registros',
                '_': 'Mostrar %d registros'
            }
        }
    };

    new DataTable('#eventos-relatorio-table', {
        language: language,
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
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary btn-rounded',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        titleAttr: 'PDF',
                        title: reportTitle,
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        },
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
        columnDefs: [
            {
                targets: -1,
                orderable: false,
                searchable: false
            }
        ]
    });
</script>
@endsection
