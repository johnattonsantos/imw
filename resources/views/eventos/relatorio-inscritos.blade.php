@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Inscritos no Evento', 'url' => route('eventos.relatorio.inscritos'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<style>
    .participant-summary-card {
        border: 1px solid #e0e6ed;
        border-radius: 6px;
        box-shadow: 0 8px 18px rgba(31, 45, 61, 0.08);
    }

    .participant-summary-card .card-title {
        color: #1b2e4b;
        font-size: 18px;
        font-weight: 700;
    }

    .participant-summary-card .label {
        color: #8f9bb3;
        font-size: 14px;
        letter-spacing: .02em;
    }

    .participant-summary-card .value {
        color: #4361ee;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.2;
    }

    .participant-summary-card .summary-item {
        border-right: 1px solid #e0e6ed;
    }

    .participant-summary-card .summary-item:last-child {
        border-right: 0;
    }

    @media (max-width: 767.98px) {
        .participant-summary-card .summary-item {
            border-right: 0;
            border-bottom: 1px solid #e0e6ed;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .participant-summary-card .summary-item:last-child {
            border-bottom: 0;
            margin-bottom: 0;
        }
    }
</style>
@endsection

@section('content')
@php
    $tituloRelatorio = __('INSCRITOS NO EVENTO');
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
                    <h4>{{ __('Inscritos no Evento') }}</h4>
                    <p class="pl-3">{{ __('Registros encontrados') }}: {{ $inscricoes->count() }}</p>
                </div>
            </div>
        </div>

        <div class="widget-content widget-content-area">
            <div class="row mb-4">
                <div class="col-lg-6 mb-3">
                    <div class="card participant-summary-card">
                        <div class="card-body">
                            <div class="card-title text-center mb-3">{{ __('Clérigos') }}</div>
                            <div class="row text-center">
                                <div class="col-md-3 col-6 summary-item">
                                    <div class="label">{{ __('Presentes') }}</div>
                                    <div class="value">{{ number_format((int) $participantSummary['clerigos']['presentes'], 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-3 col-6 summary-item">
                                    <div class="label">{{ __('Ausentes') }}</div>
                                    <div class="value">{{ number_format((int) $participantSummary['clerigos']['ausentes'], 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-3 col-6 summary-item">
                                    <div class="label">{{ __('% de presença') }}</div>
                                    <div class="value">{{ number_format((float) $participantSummary['clerigos']['percentual_presenca'], 2, ',', '.') }}%</div>
                                </div>
                                <div class="col-md-3 col-6 summary-item">
                                    <div class="label">{{ __('Total') }}</div>
                                    <div class="value">{{ number_format((int) $participantSummary['clerigos']['total'], 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="card participant-summary-card">
                        <div class="card-body">
                            <div class="card-title text-center mb-3">{{ __('Membros') }}</div>
                            <div class="row text-center">
                                <div class="col-md-3 col-6 summary-item">
                                    <div class="label">{{ __('Presentes') }}</div>
                                    <div class="value">{{ number_format((int) $participantSummary['membros']['presentes'], 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-3 col-6 summary-item">
                                    <div class="label">{{ __('Ausentes') }}</div>
                                    <div class="value">{{ number_format((int) $participantSummary['membros']['ausentes'], 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-3 col-6 summary-item">
                                    <div class="label">{{ __('% de presença') }}</div>
                                    <div class="value">{{ number_format((float) $participantSummary['membros']['percentual_presenca'], 2, ',', '.') }}%</div>
                                </div>
                                <div class="col-md-3 col-6 summary-item">
                                    <div class="label">{{ __('Total') }}</div>
                                    <div class="value">{{ number_format((int) $participantSummary['membros']['total'], 0, ',', '.') }}</div>
                                </div>
                            </div>
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
                        <select name="status" class="form-control form-control-sm" title="{{ __('Status do evento') }}">
                            <option value="">{{ __('Status') }}</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
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
                        <a href="{{ route('eventos.relatorio.inscritos') }}" class="btn btn-light btn-sm">{{ __('Limpar') }}</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="inscritos-evento-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ __('EVENTO') }}</th>
                            <th>{{ __('LOCAL') }}</th>
                            <th>{{ __('NOME DO INSCRITO') }}</th>
                            <th>{{ __('TIPO DO PARTICIPANTE') }}</th>
                            <th>{{ __('FUNÇÃO ECLESIÁSTICA') }}</th>
                            <th>{{ __('IGREJA VINCULADA') }}</th>
                            <th>{{ __('TELEFONE') }}</th>
                            <th>{{ __('CPF') }}</th>
                            <th>{{ __('QR CODE') }}</th>
                            <th>{{ __('DATA DA INSCRIÇÃO') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inscricoes as $inscricao)
                            @php
                                $evento = $inscricao->evento;
                                $localEvento = $evento->evento_local_nome ?? '-';
                                if (!empty($evento->local)) {
                                    $localEvento = $localEvento !== '-' ? $localEvento . ' - ' . $evento->local : $evento->local;
                                }
                            @endphp
                            <tr>
                                <td>{{ $evento->titulo }}</td>
                                <td>{{ $localEvento }}</td>
                                <td>{{ $inscricao->nome }}</td>
                                <td>{{ $inscricao->tipo_participante === 'clerigo' ? __('Clérigo') : __('Membro') }}</td>
                                <td>{{ $inscricao->funcao_eclesiastica ?: '-' }}</td>
                                <td>{{ $inscricao->igreja_nome ?: '-' }}</td>
                                <td>{{ $inscricao->telefone ?: '-' }}</td>
                                <td>{{ $formatCpf($inscricao->cpf) }}</td>
                                <td>
                                    <div class="evento-qrcode" data-token="{{ $inscricao->qr_token }}" aria-label="{{ __('QR Code do inscrito') }}"></div>
                                </td>
                                <td>{{ optional($inscricao->created_at)->format('d/m/Y H:i') }}</td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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

    new DataTable('#inscritos-evento-table', {
        language: language,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, @json(__('Todos'))]],
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
                        pageSize: 'A4',
                        customize: function (doc) {
                            const tableNode = doc.content.find(function (item) {
                                return item.table;
                            });

                            doc.defaultStyle.fontSize = 8;
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

    if (typeof QRCode !== 'undefined') {
        document.querySelectorAll('.evento-qrcode[data-token]').forEach(function (element) {
            if (!element.dataset.token) {
                element.textContent = '-';
                return;
            }

            new QRCode(element, {
                text: element.dataset.token,
                width: 72,
                height: 72,
                correctLevel: QRCode.CorrectLevel.M
            });
        });
    }
</script>
@endsection
