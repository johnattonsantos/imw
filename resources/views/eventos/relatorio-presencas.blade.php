@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Histórico de Presença', 'url' => route('eventos.relatorio.presencas'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<style>
    .presence-summary-card {
        min-height: 105px;
        border: 1px solid #e0e6ed;
        border-radius: 6px;
        box-shadow: 0 8px 18px rgba(31, 45, 61, 0.08);
    }

    .presence-summary-card .label {
        color: #8f9bb3;
        font-size: 14px;
        letter-spacing: .02em;
    }

    .presence-summary-card .value {
        color: #4361ee;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.2;
    }
</style>
@endsection

@section('content')
@php
    $tituloRelatorio = __('HISTÓRICO DE PRESENÇA');
    $formatCpf = function ($cpf) {
        $digits = preg_replace('/\D/', '', (string) $cpf);

        if (strlen($digits) !== 11) {
            return $cpf ?: '-';
        }

        return substr($digits, 0, 3) . '.' . substr($digits, 3, 3) . '.' . substr($digits, 6, 3) . '-' . substr($digits, 9, 2);
    };
@endphp

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-12">
                    <h4>{{ __('Histórico de Presença') }}</h4>
                    <p class="pl-3">{{ __('Registros encontrados') }}: {{ $movimentos->count() }}</p>
                </div>
            </div>
        </div>

        <div class="widget-content widget-content-area">
            <div class="row mb-4">
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card presence-summary-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="label">{{ __('Presentes') }}</div>
                            <div class="value">{{ number_format((int) $presenceSummary['presentes'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card presence-summary-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="label">{{ __('Ausentes') }}</div>
                            <div class="value">{{ number_format((int) $presenceSummary['ausentes'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="GET" class="mb-3">
                <div class="row align-items-center">
                    <div class="col-lg-3 mb-2">
                        <select name="evento_id" class="form-control form-control-sm" title="{{ __('Evento') }}">
                            <option value="">{{ __('Todos os eventos') }}</option>
                            @foreach ($eventOptions as $eventOption)
                                <option value="{{ $eventOption->id }}" {{ (string) request('evento_id') === (string) $eventOption->id ? 'selected' : '' }}>
                                    {{ optional($eventOption->data_inicio)->format('d/m/Y') }} - {{ $eventOption->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 mb-2">
                        <select name="tipo" class="form-control form-control-sm" title="{{ __('Movimento') }}">
                            <option value="">{{ __('Todos') }}</option>
                            <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>{{ __('Entrada') }}</option>
                            <option value="saida" {{ request('tipo') === 'saida' ? 'selected' : '' }}>{{ __('Saída') }}</option>
                        </select>
                    </div>

                    <div class="col-lg-2 mb-2">
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="form-control form-control-sm" title="{{ __('Data inicial') }}">
                    </div>

                    <div class="col-lg-2 mb-2">
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="form-control form-control-sm" title="{{ __('Data final') }}">
                    </div>

                    <div class="col-lg-3 mb-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('Pesquisar por nome, CPF, igreja ou evento') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Filtrar') }}</button>
                        <a href="{{ route('eventos.relatorio.presencas') }}" class="btn btn-light btn-sm">{{ __('Limpar') }}</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="presencas-evento-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('EVENTO') }}</th>
                            <th>{{ __('LOCAL') }}</th>
                            <th>{{ __('NOME DO INSCRITO') }}</th>
                            <th>{{ __('CPF') }}</th>
                            <th>{{ __('IGREJA VINCULADA') }}</th>
                            <th>{{ __('MOVIMENTO') }}</th>
                            <th>{{ __('DATA/HORA') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movimentos as $movimento)
                            @php
                                $evento = $movimento->evento;
                                $inscricao = $movimento->inscricao;
                                $localEvento = $evento->evento_local_nome ?? '-';
                                if (!empty($evento->local)) {
                                    $localEvento = $localEvento !== '-' ? $localEvento . ' - ' . $evento->local : $evento->local;
                                }
                            @endphp
                            <tr>
                                <td>{{ $evento->titulo }}</td>
                                <td>{{ $localEvento }}</td>
                                <td>{{ optional($inscricao)->nome ?: '-' }}</td>
                                <td>{{ $formatCpf(optional($inscricao)->cpf) }}</td>
                                <td>{{ optional($inscricao)->igreja_nome ?: '-' }}</td>
                                <td>{{ $movimento->tipo === 'entrada' ? __('Entrada') : __('Saída') }}</td>
                                <td>{{ optional($movimento->registrado_em)->format('d/m/Y H:i:s') }}</td>
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
        emptyTable: @json(__('Nenhum registro encontrado')),
        info: @json(__('Mostrando de _START_ até _END_ de _TOTAL_ registros')),
        infoEmpty: @json(__('Mostrando 0 até 0 de 0 registros')),
        infoFiltered: @json(__('(Filtrados de _MAX_ registros)')),
        lengthMenu: @json(__('_MENU_ resultados por página')),
        loadingRecords: @json(__('Carregando...')),
        processing: @json(__('Processando...')),
        search: @json(__('Pesquisar')),
        zeroRecords: @json(__('Nenhum registro encontrado')),
        paginate: {
            first: @json(__('Primeiro')),
            last: @json(__('Último')),
            next: @json(__('Próximo')),
            previous: @json(__('Anterior'))
        },
        buttons: {
            pageLength: {'-1': @json(__('Mostrar todos os registros')), '_': @json(__('Mostrar %d registros'))}
        }
    };

    new DataTable('#presencas-evento-table', {
        language: language,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, @json(__('Todos'))]],
        layout: {
            topStart: {
                buttons: [
                    'pageLength',
                    {extend: 'excel', className: 'btn btn-primary btn-rounded', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', title: reportTitle},
                    {extend: 'pdf', className: 'btn btn-primary btn-rounded', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', title: reportTitle, orientation: 'landscape', pageSize: 'A4'}
                ]
            },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        }
    });
</script>
@endsection
