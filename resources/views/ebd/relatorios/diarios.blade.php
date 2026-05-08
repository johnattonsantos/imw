@extends('template.layout')

@section('extras-css')
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
    <style>
        .ebd-filtros .row {
            row-gap: 8px;
        }

        .ebd-filtros .form-group {
            margin-bottom: 0;
        }

        .ebd-filtros .filtro-acoes {
            display: flex;
            gap: 8px;
            align-items: flex-end;
            height: 100%;
            flex-wrap: wrap;
        }
    </style>
@endsection

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'EBD', 'url' => route('ebd.dashboard'), 'active' => false],
        ['text' => 'Relatórios', 'url' => '#', 'active' => false],
        ['text' => 'Diário', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3">
                <h5 class="mb-0">Diário EBD</h5>
            </div>
            <div class="widget-content widget-content-area">
                <form method="GET" action="{{ route('ebd.relatorios.diarios') }}" class="mb-4 ebd-filtros">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label for="turma_id" class="mb-1">Turma</label>
                            <select id="turma_id" name="turma_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($turmasFiltro as $turma)
                                    <option value="{{ $turma->id }}" {{ (string) ($filters['turma_id'] ?? '') === (string) $turma->id ? 'selected' : '' }}>
                                        {{ $turma->nome }} ({{ $turma->ano }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="periodo_aula" class="mb-1">Período</label>
                            <select id="periodo_aula" name="periodo_aula" class="form-control">
                                <option value="">Todos</option>
                                <option value="manha" {{ ($filters['periodo_aula'] ?? '') === 'manha' ? 'selected' : '' }}>Manhã</option>
                                <option value="noite" {{ ($filters['periodo_aula'] ?? '') === 'noite' ? 'selected' : '' }}>Noite</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="data_inicio" class="mb-1">Data início</label>
                            <input type="date" id="data_inicio" name="data_inicio" class="form-control" value="{{ $filters['data_inicio'] ?? '' }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="data_fim" class="mb-1">Data fim</label>
                            <input type="date" id="data_fim" name="data_fim" class="form-control" value="{{ $filters['data_fim'] ?? '' }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="q" class="mb-1">Busca</label>
                            <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                                placeholder="Tema, conteúdo, turma, professor...">
                        </div>
                        <div class="col-md-12 mt-2 filtro-acoes">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('ebd.relatorios.diarios') }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm display nowrap" id="ebd-relatorio-diarios" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Turma</th>
                                <th>Classe</th>
                                <th>Professor</th>
                                <th>Período</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Tema</th>
                                <th>Presenças</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($diarios as $item)
                                <tr>
                                    <td>{{ optional($item->data_aula)->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $item->turma->nome ?? '-' }}</td>
                                    <td>{{ $item->turma->classe->nome ?? '-' }}</td>
                                    <td>{{ $item->turma->professor->membro->nome ?? '-' }}</td>
                                    <td>{{ ucfirst($item->periodo_aula ?? '-') }}</td>
                                    <td>{{ $item->hora_inicio ? substr($item->hora_inicio, 0, 5) : '-' }}</td>
                                    <td>{{ $item->hora_fim ? substr($item->hora_fim, 0, 5) : '-' }}</td>
                                    <td>{{ $item->tema_aula ?? '-' }}</td>
                                    <td>{{ $item->total_presentes }}/{{ $item->presencas_count }}</td>
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
        new DataTable('#ebd-relatorio-diarios', {
            pageLength: 25,
            order: [[0, 'desc']],
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: 'EBD - DIÁRIO'
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            title: 'EBD - DIÁRIO'
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
