@extends('template.layout')

@section('extras-css')
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'EBD', 'url' => route('ebd.dashboard'), 'active' => false],
        ['text' => 'Relatórios', 'url' => '#', 'active' => false],
        ['text' => 'Alunos', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3">
                <h5 class="mb-0">Alunos EBD</h5>
            </div>
            <div class="widget-content widget-content-area">
                <form method="GET" action="{{ route('ebd.relatorios.alunos') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label for="q" class="mb-1">Busca</label>
                            <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                                placeholder="Nome, CPF, telefone, e-mail">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="ativo" class="mb-1">Ativo na EBD</label>
                            <select id="ativo" name="ativo" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" {{ ($filters['ativo'] ?? '') === '1' ? 'selected' : '' }}>Ativos</option>
                                <option value="0" {{ ($filters['ativo'] ?? '') === '0' ? 'selected' : '' }}>Inativos</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="status_membro" class="mb-1">Status membro</label>
                            <select id="status_membro" name="status_membro" class="form-control">
                                <option value="">Todos</option>
                                <option value="A" {{ ($filters['status_membro'] ?? '') === 'A' ? 'selected' : '' }}>Ativo</option>
                                <option value="I" {{ ($filters['status_membro'] ?? '') === 'I' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="vinculo" class="mb-1">Vínculo</label>
                            <select id="vinculo" name="vinculo" class="form-control">
                                <option value="">Todos</option>
                                <option value="M" {{ ($filters['vinculo'] ?? '') === 'M' ? 'selected' : '' }}>Membro</option>
                                <option value="C" {{ ($filters['vinculo'] ?? '') === 'C' ? 'selected' : '' }}>Congregado</option>
                                <option value="V" {{ ($filters['vinculo'] ?? '') === 'V' ? 'selected' : '' }}>Visitante</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="turma_id" class="mb-1">Turma</label>
                            <select id="turma_id" name="turma_id" class="form-control">
                                <option value="">Todas</option>
                                @foreach ($turmasFiltro as $turma)
                                    <option value="{{ $turma->id }}"
                                        {{ (string) ($filters['turma_id'] ?? '') === (string) $turma->id ? 'selected' : '' }}>
                                        {{ $turma->nome }} ({{ $turma->ano }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('ebd.relatorios.alunos') }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm display nowrap" id="ebd-relatorio-alunos" style="width: 100%;">
                        <thead>
                            <tr>
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
                            @forelse ($alunos as $item)
                                <tr>
                                    <td>{{ $item->membro->nome ?? '-' }}</td>
                                    <td>{{ $item->membro->cpf ?? '-' }}</td>
                                    <td>{{ $item->membro->contato->telefone_preferencial ?? $item->membro->contato->telefone_whatsapp ?? $item->membro->contato->telefone_alternativo ?? '-' }}</td>
                                    <td>{{ $item->membro->contato->email_preferencial ?? $item->membro->contato->email_alternativo ?? '-' }}</td>
                                    <td>{{ $item->membro->vinculo_text ?? '-' }}</td>
                                    <td>{{ $item->membro->status_text ?? '-' }}</td>
                                    <td>{{ $item->ativo ? 'Sim' : 'Não' }}</td>
                                    <td>{{ $item->total_turmas_ativas }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nenhum aluno encontrado.</td>
                                </tr>
                            @endforelse
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
        new DataTable('#ebd-relatorio-alunos', {
            pageLength: 25,
            order: [[0, 'asc']],
            layout: {
                topStart: {
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            title: 'EBD - ALUNOS'
                        },
                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            className: 'btn btn-primary btn-rounded',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            title: 'EBD - ALUNOS'
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
