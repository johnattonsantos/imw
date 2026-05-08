@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
        ['text' => 'EBD - Alunos', 'url' => '#', 'active' => true],
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
                        <h4>Relatório EBD - Alunos (Regional)</h4>
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
                                    <option value="{{ $distrito->id }}" {{ (string) ($filters['distrito_id'] ?? '') === (string) $distrito->id ? 'selected' : '' }}>
                                        {{ $distrito->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="control-label">Igreja:</label>
                            <select name="igreja_id" id="igreja_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($igrejas as $igreja)
                                    <option value="{{ $igreja->id }}" {{ (string) ($filters['igreja_id'] ?? '') === (string) $igreja->id ? 'selected' : '' }}>
                                        {{ $igreja->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label class="control-label">Status EBD:</label>
                            <select name="ativo" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ ($filters['ativo'] ?? '') === '1' ? 'selected' : '' }}>Ativos</option>
                                <option value="0" {{ ($filters['ativo'] ?? '') === '0' ? 'selected' : '' }}>Inativos</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label class="control-label">Status membro:</label>
                            <select name="status_membro" class="form-control">
                                <option value="">Todos</option>
                                <option value="A" {{ ($filters['status_membro'] ?? '') === 'A' ? 'selected' : '' }}>Ativo</option>
                                <option value="I" {{ ($filters['status_membro'] ?? '') === 'I' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label class="control-label">Vínculo:</label>
                            <select name="vinculo" class="form-control">
                                <option value="">Todos</option>
                                <option value="M" {{ ($filters['vinculo'] ?? '') === 'M' ? 'selected' : '' }}>Membro</option>
                                <option value="C" {{ ($filters['vinculo'] ?? '') === 'C' ? 'selected' : '' }}>Congregado</option>
                                <option value="V" {{ ($filters['vinculo'] ?? '') === 'V' ? 'selected' : '' }}>Visitante</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="control-label">Turma:</label>
                            <select name="turma_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($turmasFiltro as $turma)
                                    <option value="{{ $turma->id }}" {{ (string) ($filters['turma_id'] ?? '') === (string) $turma->id ? 'selected' : '' }}>
                                        {{ $turma->nome }} ({{ $turma->ano }}) - {{ $turma->classe->nome ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="control-label">Busca:</label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control"
                                placeholder="Nome, CPF, telefone, e-mail..." />
                        </div>
                        <div class="col-lg-2 col-md-12 filtro-acoes">
                            <button type="submit" class="btn btn-primary"><x-bx-search /> Buscar</button>
                            <a href="{{ url()->current() }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped display nowrap" id="ebd-regional-alunos-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Distrito</th>
                                <th>Igreja</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Telefone</th>
                                <th>E-mail</th>
                                <th>Vínculo</th>
                                <th>Status membro</th>
                                <th>Ativo EBD</th>
                                <th>Turmas ativas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alunos as $item)
                                <tr>
                                    <td>{{ $item->membro->distrito->nome ?? '-' }}</td>
                                    <td>{{ $item->membro->igreja->nome ?? '-' }}</td>
                                    <td>{{ $item->membro->nome ?? '-' }}</td>
                                    <td>{{ $item->membro->cpf ?? '-' }}</td>
                                    <td>{{ $item->membro->contato->telefone_preferencial ?? $item->membro->contato->telefone_whatsapp ?? $item->membro->contato->telefone_alternativo ?? '-' }}</td>
                                    <td>{{ $item->membro->contato->email_preferencial ?? $item->membro->contato->email_alternativo ?? '-' }}</td>
                                    <td>{{ $item->membro->vinculo_text ?? '-' }}</td>
                                    <td>{{ $item->membro->status_text ?? '-' }}</td>
                                    <td>{{ $item->ativo ? 'Sim' : 'Não' }}</td>
                                    <td>{{ $item->total_turmas_ativas }}</td>
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
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
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
            .catch(() => {
                igrejaSelect.innerHTML = '<option value="">Todas</option>';
            });
        });

        new DataTable('#ebd-regional-alunos-table', {
            order: [[0, 'asc'], [1, 'asc'], [2, 'asc']],
            pageLength: 25,
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: 'IMW - RELATÓRIO EBD ALUNOS (REGIONAL)'
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            title: 'IMW - RELATÓRIO EBD ALUNOS (REGIONAL)'
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
