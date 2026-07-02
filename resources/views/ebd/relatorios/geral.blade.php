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
        ['text' => 'Relatório Geral EBD', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3">
                <h5 class="mb-0">Relatório Geral EBD</h5>
            </div>
            <div class="widget-content widget-content-area">
                <form method="GET" action="{{ route('ebd.relatorios.geral') }}" class="mb-4 ebd-filtros">
                    <div class="row">
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
                            <label for="turma_id" class="mb-1">EBD</label>
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
                            <label for="tipo_unidade" class="mb-1">Congregação</label>
                            <select id="tipo_unidade" name="tipo_unidade" class="form-control">
                                <option value="">Todas</option>
                                <option value="SEDE" {{ ($filters['tipo_unidade'] ?? '') === 'SEDE' ? 'selected' : '' }}>Sede</option>
                                <option value="CONGREGACAO" {{ ($filters['tipo_unidade'] ?? '') === 'CONGREGACAO' ? 'selected' : '' }}>Congregação</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="presenca_status" class="mb-1">Presença</label>
                            <select id="presenca_status" name="presenca_status" class="form-control">
                                <option value="">Todas</option>
                                <option value="PRESENTE" {{ ($filters['presenca_status'] ?? '') === 'PRESENTE' ? 'selected' : '' }}>Presente</option>
                                <option value="AUSENTE" {{ ($filters['presenca_status'] ?? '') === 'AUSENTE' ? 'selected' : '' }}>Ausente</option>
                                <option value="NAO_LANCADA" {{ ($filters['presenca_status'] ?? '') === 'NAO_LANCADA' ? 'selected' : '' }}>Não lançada</option>
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
                        <div class="col-md-8 mb-2">
                            <label for="q" class="mb-1">Busca</label>
                            <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                                placeholder="Classe, EBD, professor, aluno, tema...">
                        </div>
                        <div class="col-md-4 mt-2 filtro-acoes">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('ebd.relatorios.geral') }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm display nowrap" id="ebd-relatorio-geral" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Igreja</th>
                                <th>Congregação</th>
                                <th>Nome da Congregação</th>
                                <th>Sala</th>
                                <th>Faixa etária</th>
                                <th>EBD</th>
                                <th>Ano</th>
                                <th>Semestre</th>
                                <th>Professor</th>
                                <th>Aluno</th>
                                <th>CPF</th>
                                <th>Data aula</th>
                                <th>Período</th>
                                <th>Tema</th>
                                <th>Presença</th>
                                <th>Justificativa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registros as $item)
                                <tr>
                                    <td>{{ $item->igreja_nome ?? '-' }}</td>
                                    <td>{{ $item->tipo_unidade ?? '-' }}</td>
                                    <td>{{ $item->congregacao_nome ?? '-' }}</td>
                                    <td>{{ $item->sala_nome ?? '-' }}</td>
                                    <td>{{ $item->sala_faixa_etaria ?? '-' }}</td>
                                    <td>{{ $item->turma_nome ?? '-' }}</td>
                                    <td>{{ $item->turma_ano ?? '-' }}</td>
                                    <td>{{ $item->turma_semestre ?? '-' }}</td>
                                    <td>{{ $item->professor_nome ?? '-' }}</td>
                                    <td>{{ $item->aluno_nome ?? '-' }}</td>
                                    <td>{{ $item->aluno_cpf ?? '-' }}</td>
                                    <td>{{ $item->data_aula ? \Carbon\Carbon::parse($item->data_aula)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $item->periodo_aula ? ucfirst($item->periodo_aula) : '-' }}</td>
                                    <td>{{ $item->tema_aula ?? '-' }}</td>
                                    <td>{{ $item->presenca_status ?? '-' }}</td>
                                    <td>{{ $item->presenca_justificativa ?? '-' }}</td>
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
        new DataTable('#ebd-relatorio-geral', {
            pageLength: 25,
            order: [[6, 'desc'], [5, 'asc'], [9, 'asc'], [11, 'desc']],
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: 'EBD - RELATORIO GERAL'
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            title: 'EBD - RELATORIO GERAL'
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
