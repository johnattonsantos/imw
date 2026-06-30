@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Pessoas do Evento', 'url' => route('eventos.relatorio.pessoas'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@php
    $tituloRelatorio = 'PESSOAS DO EVENTO';
@endphp

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-12">
                    <h4>Pessoas do Evento</h4>
                    <p class="pl-3">Registros encontrados: {{ $pessoas->count() }}</p>
                </div>
            </div>
        </div>

        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-3">
                <div class="row align-items-center">
                    <div class="col-lg-2 mb-2">
                        <select name="evento_id" class="form-control form-control-sm" title="Evento">
                            <option value="">Todos os eventos</option>
                            @foreach ($eventOptions as $eventOption)
                                <option value="{{ $eventOption->id }}" {{ (string) request('evento_id') === (string) $eventOption->id ? 'selected' : '' }}>
                                    {{ optional($eventOption->data_inicio)->format('d/m/Y') }} - {{ $eventOption->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 mb-2">
                        <select name="instituicao_id" class="form-control form-control-sm" title="Sede ou congregação">
                            <option value="">Todas as sedes/congregações</option>
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
                        <select name="evento_funcao_id" class="form-control form-control-sm" title="Função">
                            <option value="">Todas as funções</option>
                            @foreach ($funcoesEventos as $funcaoEvento)
                                <option value="{{ $funcaoEvento->id }}" {{ (string) request('evento_funcao_id') === (string) $funcaoEvento->id ? 'selected' : '' }}>
                                    {{ $funcaoEvento->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-1 mb-2">
                        <select name="lider" class="form-control form-control-sm" title="Liderança">
                            <option value="">Todos</option>
                            <option value="1" {{ request('lider') === '1' ? 'selected' : '' }}>Líder</option>
                            <option value="0" {{ request('lider') === '0' ? 'selected' : '' }}>Equipe</option>
                        </select>
                    </div>

                    <div class="col-lg-1 mb-2">
                        <select name="status" class="form-control form-control-sm" title="Status do evento">
                            <option value="">Status</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-1 mb-2">
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="form-control form-control-sm" title="Data inicial">
                    </div>

                    <div class="col-lg-1 mb-2">
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="form-control form-control-sm" title="Data final">
                    </div>

                    <div class="col-lg-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                        <a href="{{ route('eventos.relatorio.pessoas') }}" class="btn btn-light btn-sm">Limpar</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="pessoas-evento-table" style="width: 100%;">
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
                            <th>LOCAL DO EVENTO</th>
                            <th>STATUS</th>
                            <th>FUNÇÃO</th>
                            <th>NOME DA PESSOA</th>
                            <th>CONTATO</th>
                            <th>LÍDER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pessoas as $pessoa)
                            @php
                                $evento = $pessoa->evento;
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
                                <td>{{ $statusOptions[$evento->status] ?? $evento->status }}</td>
                                <td>{{ optional($pessoa->eventoFuncao)->nome ?: ($pessoa->funcao ?: '-') }}</td>
                                <td>{{ $pessoa->nome }}</td>
                                <td>{{ $pessoa->contato ?: '-' }}</td>
                                <td>{{ $pessoa->lider ? 'Sim' : 'Não' }}</td>
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
        paginate: {first: 'Primeiro', last: 'Último', next: 'Próximo', previous: 'Anterior'},
        buttons: {
            pageLength: {'-1': 'Mostrar todos os registros', '_': 'Mostrar %d registros'}
        }
    };

    new DataTable('#pessoas-evento-table', {
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
                        title: reportTitle
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary btn-rounded',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        titleAttr: 'PDF',
                        title: reportTitle,
                        orientation: 'landscape',
                        pageSize: 'A3',
                        customize: function (doc) {
                            const tableNode = doc.content.find(function (item) {
                                return item.table;
                            });

                            doc.defaultStyle.fontSize = 7;
                            doc.pageMargins = [18, 28, 18, 28];
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
        }
    });
</script>
@endsection
