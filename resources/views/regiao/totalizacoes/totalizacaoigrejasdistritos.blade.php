@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Totalização', 'url' => '#', 'active' => false],
        ['text' => 'Igrejas por Distrito', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.css') }}" />
    <style>
        .igrejas-distrito-wrapper {
            max-width: 720px;
            margin: 0 auto;
        }

        .igrejas-distrito-table {
            border-collapse: collapse;
            width: 100%;
            font-size: 92%;
        }

        .igrejas-distrito-table td {
            border: 1px solid #1f1f1f;
            color: #111;
            padding: 6px 10px;
            vertical-align: middle;
        }

        .igrejas-distrito-table .distrito-header td {
            background: #ffff00;
            color: #000;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            padding: 12px 10px;
        }

        .igrejas-distrito-table .igreja-linha td {
            background: #fff;
            text-transform: uppercase;
        }

        .igrejas-distrito-table .subtotal-linha td,
        .igrejas-distrito-table .total-geral-linha td {
            background: #f2f2f2;
            font-weight: 700;
            text-transform: uppercase;
        }

        .igrejas-distrito-table .espaco-linha td {
            border-left: 0;
            border-right: 0;
            height: 18px;
            padding: 0;
            background: #fff;
        }
    </style>
@endsection

@include('extras.alerts')

@php
    $excelParams = !empty($distritoSelecionado) ? ['distrito_id' => $distritoSelecionado] : [];
@endphp

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Igrejas por Distrito') }} - {{ $regiao->nome }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('regiao.relatorio.igrejas') }}" class="mb-4">
                            <div class="row align-items-end">
                                <div class="col-lg-6 col-md-8 col-12">
                                    <label for="distrito_id">{{ __('Distrito') }}</label>
                                    <select id="distrito_id" name="distrito_id" class="form-control selectpicker" data-live-search="true" data-size="8">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach ($distritos as $distrito)
                                            <option value="{{ $distrito->id }}" {{ (int) $distritoSelecionado === (int) $distrito->id ? 'selected' : '' }}>
                                                {{ $distrito->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-4 col-12 mt-3 mt-md-0">
                                    <button type="submit" class="btn btn-primary btn-rounded">
                                        <i class="fas fa-search"></i> {{ __('Buscar') }}
                                    </button>
                                    <a href="{{ route('regiao.relatorio.igrejas') }}" class="btn btn-secondary btn-rounded">
                                        {{ __('Limpar') }}
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="row align-items-center mb-3">
                            <div class="col-md-8 col-12">
                                <h6 class="mt-2 text-uppercase">
                                    {{ __('Relação de Igrejas por Distrito') }} - {{ $regiao->nome }}
                                </h6>
                                <p class="mb-0">
                                    {{ __('Total da Região') }}: <strong>{{ $totalIgrejasRegiao }}</strong>
                                </p>
                            </div>
                            <div class="col-md-4 col-12 text-md-right mt-3 mt-md-0">
                                <a href="{{ route('regiao.relatorio.igrejas-excel', $excelParams) }}" class="btn btn-primary btn-rounded">
                                    <i class="fas fa-file-excel"></i> {{ __('Excel') }}
                                </a>
                                <button type="button" class="btn btn-primary btn-rounded" onclick="exportIgrejasDistritoPdf();">
                                    <i class="fas fa-file-pdf"></i> {{ __('PDF') }}
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive igrejas-distrito-wrapper">
                            <table id="igrejas-distrito-table" class="igrejas-distrito-table">
                                <tbody>
                                    @forelse ($igrejasPorDistrito as $grupo)
                                        <tr class="distrito-header" data-export-type="district">
                                            <td>{{ $grupo->distrito_nome }}</td>
                                        </tr>
                                        @forelse ($grupo->igrejas as $igreja)
                                            <tr class="igreja-linha" data-export-type="church">
                                                <td>{{ $igreja->igreja_nome }}</td>
                                            </tr>
                                        @empty
                                            <tr class="igreja-linha" data-export-type="church">
                                                <td>{{ __('Nenhuma igreja ativa') }}</td>
                                            </tr>
                                        @endforelse
                                        <tr class="subtotal-linha" data-export-type="subtotal">
                                            <td>{{ __('Total do Distrito') }}: {{ $grupo->total }}</td>
                                        </tr>
                                        <tr class="espaco-linha" data-export-type="blank">
                                            <td></td>
                                        </tr>
                                    @empty
                                        <tr class="igreja-linha" data-export-type="church">
                                            <td>{{ __('Nenhum distrito encontrado para esta região.') }}</td>
                                        </tr>
                                    @endforelse
                                    <tr class="total-geral-linha" data-export-type="total">
                                        <td>{{ __('Total Geral da Região') }}: {{ $totalIgrejasRegiao }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker(window.IMW_SELECTPICKER_OPTIONS || {});
        });

        function getIgrejasDistritoRows() {
            const table = document.getElementById('igrejas-distrito-table');
            if (!table) {
                return [];
            }

            return Array.from(table.querySelectorAll('tbody tr')).map(function(row) {
                return {
                    type: row.dataset.exportType || 'church',
                    value: row.querySelector('td').innerText.trim()
                };
            });
        }

        function exportIgrejasDistritoPdf() {
            const rows = getIgrejasDistritoRows();
            if (!rows.length) {
                return;
            }

            const body = rows.map(function(row) {
                const cell = {
                    text: row.value,
                    alignment: row.type === 'church' ? 'left' : 'center',
                    bold: ['district', 'subtotal', 'total'].includes(row.type),
                    margin: row.type === 'blank' ? [0, 4, 0, 4] : [4, 2, 4, 2]
                };

                if (row.type === 'district') {
                    cell.fillColor = '#ffff00';
                    cell.color = '#000000';
                }

                if (row.type === 'blank') {
                    cell.border = [false, false, false, false];
                }

                return [cell];
            });

            pdfMake.createPdf({
                pageOrientation: 'portrait',
                pageSize: 'A4',
                content: [
                    { text: '{{ __('Igrejas por Distrito') }} - {{ $regiao->nome }}', style: 'header' },
                    {
                        table: {
                            widths: ['*'],
                            body: body
                        }
                    }
                ],
                styles: {
                    header: {
                        fontSize: 14,
                        bold: true,
                        alignment: 'center',
                        margin: [0, 0, 0, 10]
                    }
                },
                defaultStyle: {
                    fontSize: 10
                }
            }).download('igrejas-por-distrito.pdf');
        }
    </script>
@endsection
