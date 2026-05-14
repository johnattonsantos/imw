@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Relatórios Distritais', 'url' => '#', 'active' => false],
        ['text' => 'EBD - Agenda', 'url' => '#', 'active' => true],
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
                        <h4>Relatório EBD - Agenda (Distrital)</h4>
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
                        <div class="col-lg-3 col-md-6">
                            <label class="control-label">EBD:</label>
                            <select name="turma_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($turmasFiltro as $turma)
                                    <option value="{{ $turma->id }}" {{ (string) ($filters['turma_id'] ?? '') === (string) $turma->id ? 'selected' : '' }}>{{ $turma->nome }} ({{ $turma->ano }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="control-label">Data início:</label>
                            <input type="date" name="data_inicio" class="form-control" value="{{ $filters['data_inicio'] ?? '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="control-label">Data fim:</label>
                            <input type="date" name="data_fim" class="form-control" value="{{ $filters['data_fim'] ?? '' }}">
                        </div>
                        <div class="col-lg-7 col-md-12">
                            <label class="control-label">Busca:</label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Título, local, EBD, professor..." />
                        </div>
                        <div class="col-lg-2 col-md-12 filtro-acoes">
                            <button type="submit" class="btn btn-primary"><x-bx-search /> Buscar</button>
                            <a href="{{ url()->current() }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped display nowrap" id="ebd-regional-agendas-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Distrito</th>
                                <th>Igreja</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Título</th>
                                <th>EBD</th>
                                <th>Classe</th>
                                <th>Professor</th>
                                <th>Local</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($agendas as $item)
                                <tr>
                                    <td>{{ optional(optional(optional(optional($item->turma)->professor)->membro)->distrito)->nome ?? '-' }}</td>
                                    <td>{{ optional(optional(optional(optional($item->turma)->professor)->membro)->igreja)->nome ?? '-' }}</td>
                                    <td>{{ optional($item->data_inicio)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ optional($item->data_fim)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ $item->titulo }}</td>
                                    <td>{{ optional($item->turma)->nome ?? '-' }}</td>
                                    <td>{{ optional(optional($item->turma)->classe)->nome ?? '-' }}</td>
                                    <td>{{ optional(optional(optional($item->turma)->professor)->membro)->nome ?? '-' }}</td>
                                    <td>{{ $item->local ?? '-' }}</td>
                                    <td>{{ $item->descricao ?? '-' }}</td>
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

        new DataTable('#ebd-regional-agendas-table', {
            order: [[2, 'desc']],
            pageLength: 25,
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'excel', className: 'btn btn-primary btn-rounded', text: '<i class="fas fa-file-excel"></i> Excel', title: 'IMW - RELATÓRIO EBD AGENDA (DISTRITAL)' },
                        { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', className: 'btn btn-primary btn-rounded', text: '<i class="fas fa-file-pdf"></i> PDF', title: 'IMW - RELATÓRIO EBD AGENDA (DISTRITAL)' }
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
