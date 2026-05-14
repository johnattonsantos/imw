@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Relatórios Distritais', 'url' => '#', 'active' => false],
        ['text' => 'Relatório Geral EBD', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

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

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Relatório Geral EBD (Distrital)</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form method="GET" class="form-vertical ebd-filtros">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="control-label">Distrito:</label>
                            <select name="distrito_id" id="distrito_id" class="form-control">
                                <option value="">Todos</option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito->id }}" {{ (string) ($filters['distrito_id'] ?? '') === (string) $distrito->id ? 'selected' : '' }}>{{ $distrito->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="control-label">Igreja:</label>
                            <select name="igreja_id" id="igreja_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($igrejas as $igreja)
                                    <option value="{{ $igreja->id }}" {{ (string) ($filters['igreja_id'] ?? '') === (string) $igreja->id ? 'selected' : '' }}>{{ $igreja->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="control-label">Classe:</label>
                            <select name="classe_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($classesFiltro as $classe)
                                    <option value="{{ $classe->id }}" {{ (string) ($filters['classe_id'] ?? '') === (string) $classe->id ? 'selected' : '' }}>{{ $classe->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="control-label">EBD:</label>
                            <select name="turma_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($turmasFiltro as $turma)
                                    <option value="{{ $turma->id }}" {{ (string) ($filters['turma_id'] ?? '') === (string) $turma->id ? 'selected' : '' }}>{{ $turma->nome }} ({{ $turma->ano }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="control-label">Congregação:</label>
                            <select name="tipo_unidade" class="form-control">
                                <option value="">Todas</option>
                                <option value="SEDE" {{ ($filters['tipo_unidade'] ?? '') === 'SEDE' ? 'selected' : '' }}>Sede</option>
                                <option value="CONGREGACAO" {{ ($filters['tipo_unidade'] ?? '') === 'CONGREGACAO' ? 'selected' : '' }}>Congregação</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="control-label">Presença:</label>
                            <select name="presenca_status" class="form-control">
                                <option value="">Todas</option>
                                <option value="PRESENTE" {{ ($filters['presenca_status'] ?? '') === 'PRESENTE' ? 'selected' : '' }}>Presente</option>
                                <option value="AUSENTE" {{ ($filters['presenca_status'] ?? '') === 'AUSENTE' ? 'selected' : '' }}>Ausente</option>
                                <option value="NAO_LANCADA" {{ ($filters['presenca_status'] ?? '') === 'NAO_LANCADA' ? 'selected' : '' }}>Não lançada</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="control-label">Data início:</label>
                            <input type="date" name="data_inicio" class="form-control" value="{{ $filters['data_inicio'] ?? '' }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="control-label">Data fim:</label>
                            <input type="date" name="data_fim" class="form-control" value="{{ $filters['data_fim'] ?? '' }}">
                        </div>
                        <div class="col-lg-8 col-md-12">
                            <label class="control-label">Busca:</label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Classe, EBD, professor, aluno, tema..." />
                        </div>
                        <div class="col-lg-4 col-md-12 filtro-acoes">
                            <button type="submit" class="btn btn-primary"><x-bx-search /> Buscar</button>
                            <a href="{{ url()->current() }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped display nowrap" id="ebd-regional-geral-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Distrito</th>
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
                                    <td>{{ $item->distrito_nome ?? '-' }}</td>
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
        const distritoSelect = document.getElementById('distrito_id');
        const igrejaSelect = document.getElementById('igreja_id');
        const todasIgrejasHtml = igrejaSelect ? igrejaSelect.innerHTML : '';
        distritoSelect?.addEventListener('change', function() {
            const distritoId = this.value;
            const igrejaSelecionadaAtual = igrejaSelect.value;
            if (!distritoId) {
                igrejaSelect.innerHTML = todasIgrejasHtml;
                igrejaSelect.value = '';
                return;
            }
            igrejaSelect.innerHTML = '<option value="">Carregando...</option>';
            fetch(`/instituicoes/igrejasByDistrito/${distritoId}`, {
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
            })
            .then(response => response.ok ? response.json() : Promise.reject())
            .then(igrejas => {
                let options = '<option value="">Todas</option>';
                igrejas.forEach(igreja => {
                    const selected = String(igreja.id) === String(igrejaSelecionadaAtual) ? 'selected' : '';
                    options += `<option value="${igreja.id}" ${selected}>${igreja.nome}</option>`;
                });
                igrejaSelect.innerHTML = options;
            })
            .catch(() => { igrejaSelect.innerHTML = '<option value="">Todas</option>'; });
        });

        new DataTable('#ebd-regional-geral-table', {
            order: [[0, 'asc'], [1, 'asc'], [7, 'desc'], [6, 'asc'], [10, 'asc']],
            pageLength: 25,
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'excel', className: 'btn btn-primary btn-rounded', text: '<i class="fas fa-file-excel"></i> Excel', title: 'IMW - RELATORIO GERAL EBD (DISTRITAL)' },
                        { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', className: 'btn btn-primary btn-rounded', text: '<i class="fas fa-file-pdf"></i> PDF', title: 'IMW - RELATORIO GERAL EBD (DISTRITAL)' }
                    ]
                },
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
            language: {
                decimal: ',', thousands: '.', processing: 'Processando...', loadingRecords: 'Carregando...',
                lengthMenu: 'Exibir _MENU_ resultados por página', zeroRecords: 'Nenhum registro encontrado', emptyTable: 'Nenhum registro encontrado',
                info: 'Mostrando de _START_ até _END_ de _TOTAL_ registros', infoEmpty: 'Mostrando 0 até 0 de 0 registros', infoFiltered: '(filtrado de _MAX_ registros no total)', search: 'Pesquisar',
                paginate: { first: 'Primeira', previous: 'Anterior', next: 'Próxima', last: 'Última' },
                aria: { sortAscending: ': ativar para ordenar a coluna de forma crescente', sortDescending: ': ativar para ordenar a coluna de forma decrescente' }
            }
        });
    </script>
@endsection
