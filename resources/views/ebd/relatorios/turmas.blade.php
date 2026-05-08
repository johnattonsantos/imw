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
        ['text' => 'Turmas', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3">
                <h5 class="mb-0">Turmas EBD</h5>
            </div>
            <div class="widget-content widget-content-area">
                <form method="GET" action="{{ route('ebd.relatorios.turmas') }}" class="mb-4 ebd-filtros">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label for="q" class="mb-1">Busca</label>
                            <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                                placeholder="Turma, classe, professor...">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="classe_id" class="mb-1">Classe</label>
                            <select id="classe_id" name="classe_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($classesFiltro as $classe)
                                    <option value="{{ $classe->id }}" {{ (string) ($filters['classe_id'] ?? '') === (string) $classe->id ? 'selected' : '' }}>
                                        {{ $classe->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="ano" class="mb-1">Ano</label>
                            <input type="number" id="ano" name="ano" class="form-control" value="{{ $filters['ano'] ?? '' }}" min="1900" max="9999">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="semestre" class="mb-1">Semestre</label>
                            <select id="semestre" name="semestre" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ ($filters['semestre'] ?? '') === '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ ($filters['semestre'] ?? '') === '2' ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="ativo" class="mb-1">Status</label>
                            <select id="ativo" name="ativo" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ ($filters['ativo'] ?? '') === '1' ? 'selected' : '' }}>Ativos</option>
                                <option value="0" {{ ($filters['ativo'] ?? '') === '0' ? 'selected' : '' }}>Inativos</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-2 filtro-acoes">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('ebd.relatorios.turmas') }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm display nowrap" id="ebd-relatorio-turmas" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Turma</th>
                                <th>Classe</th>
                                <th>Professor</th>
                                <th>Ano</th>
                                <th>Semestre</th>
                                <th>Ativo</th>
                                <th>Alunos ativos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($turmas as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->classe->nome ?? '-' }}</td>
                                    <td>{{ $item->professor->membro->nome ?? '-' }}</td>
                                    <td>{{ $item->ano }}</td>
                                    <td>{{ $item->semestre ?? '-' }}</td>
                                    <td>{{ $item->ativo ? 'Sim' : 'Não' }}</td>
                                    <td>{{ $item->total_alunos_ativos }}</td>
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
        new DataTable('#ebd-relatorio-turmas', {
            pageLength: 25,
            order: [[3, 'desc'], [0, 'asc']],
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: 'EBD - TURMAS'
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            title: 'EBD - TURMAS'
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
