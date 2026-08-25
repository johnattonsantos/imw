
@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Totalização', 'url' => '#', 'active' => false],
        ['text' => 'Total de Igrejas nos Distritos', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Total de Igrejas nos Distritos- {{  $regiao->nome }}</h4>
                    </div>
                </div>
            </div>
            {{-- <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="form-group row mb-4">
                        <div class="col-lg-3 text-right">
                            <label class="control-label">{{ __('* Distrito:') }}</label>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-control" id="distrito" name="distrito" required>
                                <option value="">{{ __('Selecione') }}</option>
                                <option value="all" {{ request()->input('distrito') == 'all' ? 'selected' : '' }}>{{ __('Todos') }}
                                </option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito->id }}"
                                        {{ request()->input('distrito') == $distrito->id ? 'selected' : '' }}>
                                        {{ $distrito->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-6">
                            <button id="btn_buscar" type="submit" name="action" value="buscar"
                                title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn">
                                <x-bx-search /> {{ __('Buscar') }}
                            </button>
                            <button id="btn_relatorio" type="button" class="btn btn-secondary">
                                <i class="fa fa-file-pdf"></i> {{ __('Relatório') }}
                            </button>
                        </div>
                    </div>
                </form>

                <form id="report_form" action="{{ url('regiao/relatorio/estatisticaestadocivil/pdf') }}" method="POST"
                    target="_blank" style="display: none;">
                    @csrf
                    <input type="hidden" name="distrito" id="report_distrito">
                    <input type="hidden" name="estado_civil" id="report_estado_civil">
                </form>
            </div> --}}
        </div>
    </div>


        <div class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area">
                    <!-- Conteúdo -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mt-3">TOTAL DE IGREJAS NOS DISTRITOS -
                                        {{ $regiao->nome }}</h6>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-primary btn-rounded" onclick="exportTotalIgrejasDistritosExcel();">
                                            <i class="fas fa-file-excel"></i> {{ __('Excel') }}
                                        </button>
                                        <button type="button" class="btn btn-primary btn-rounded" onclick="exportTotalIgrejasDistritosPdf();">
                                            <i class="fas fa-file-pdf"></i> {{ __('PDF') }}
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="total-igrejas-distritos-table" class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align: left;">{{ __('Distritos') }}</th>
                                                    <th style="text-align: center;">{{ __('Quantidade de Igrejas') }}</th>
                                                    <th style="text-align: center;">{{ __('Percentual') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lancamentos as $lancamento)
                                                    <tr>
                                                        <td>{{ $lancamento->nome }}</td>
                                                        <td style="text-align: center;">{{ $lancamento->total }}</td>
                                                        <td style="text-align: center;">
                                                            {{ number_format($lancamento->percentual, 2) }}%</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th style="text-align: left;">{{ __('Total Geral') }}</th>
                                                    <th style="text-align: center;">{{ $lancamentos->sum('total') }}</th>
                                                    <th style="text-align: center;">100%</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fim do Conteúdo -->
                </div>
            </div>
        </div>


@section('extras-scripts')
    <script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
    <script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
    <script src="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        function getTotalIgrejasDistritosData() {
            const table = document.getElementById('total-igrejas-distritos-table');
            if (!table) {
                return [];
            }

            const headers = Array.from(table.querySelectorAll('thead th')).map(function(th) {
                return th.innerText.trim();
            });

            const bodyRows = Array.from(table.querySelectorAll('tbody tr')).map(function(row) {
                return Array.from(row.querySelectorAll('td')).map(function(td) {
                    return td.innerText.trim();
                });
            });

            const footerRows = Array.from(table.querySelectorAll('tfoot tr')).map(function(row) {
                return Array.from(row.querySelectorAll('th, td')).map(function(cell) {
                    return cell.innerText.trim();
                });
            });

            return [headers].concat(bodyRows, footerRows);
        }

        function exportTotalIgrejasDistritosExcel() {
            const data = getTotalIgrejasDistritosData();
            if (data.length <= 1) {
                return;
            }

            const worksheet = XLSX.utils.aoa_to_sheet(data);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Igrejas por Distrito');
            XLSX.writeFile(workbook, 'igrejas-por-distrito.xlsx');
        }

        function exportTotalIgrejasDistritosPdf() {
            const data = getTotalIgrejasDistritosData();
            if (data.length <= 1) {
                return;
            }

            pdfMake.createPdf({
                pageOrientation: 'landscape',
                pageSize: 'A4',
                content: [
                    { text: '{{ __('Total de Igrejas nos Distritos') }} - {{ $regiao->nome }}', style: 'header' },
                    {
                        table: {
                            headerRows: 1,
                            widths: Array(data[0].length).fill('*'),
                            body: data
                        }
                    }
                ],
                styles: {
                    header: {
                        fontSize: 14,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    }
                },
                defaultStyle: {
                    fontSize: 9
                }
            }).download('igrejas-por-distrito.pdf');
        }

        $(document).ready(function() {
            $('.selectpicker').selectpicker(window.IMW_SELECTPICKER_OPTIONS || {});

            $('#btn_relatorio').on('click', function(event) {
                var distrito = $('#distrito').val();


                if (!distrito) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                } else {
                    $('#report_distrito').val(distrito);
                    $('#report_form').submit();
                }
            });

            $('#filter_form').submit(function(event) {
                var distrito = $('#distrito').val();


                if (!distrito) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                }
            });
        });
    </script>
@endsection
@endsection
