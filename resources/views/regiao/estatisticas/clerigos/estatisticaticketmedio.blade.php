@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Clérigo por situação', 'url' => '#', 'active' => false],
        ['text' => 'Ticket Médio', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
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
                        <h4>Ticket Médio- {{ $regiao->nome }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="form-group row mb-4" id="filtros_data_inicial">
                        <div class="col-lg-3 text-right">
                            <label class="control-label">{{ __('* Data Inicial:') }}</label>
                        </div>
                        <div class="col-lg-3">
                            <input type="date" class="form-control @error('data_inicial') is-invalid @enderror"
                                id="data_inicial" name="data_inicial" value="{{ request()->input('data_inicial') }}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row mb-4" id="filtros_data_final">
                        <div class="col-lg-3 text-right">
                            <label class="control-label">{{ __('* Data Final:') }}</label>
                        </div>
                        <div class="col-lg-3">
                            <input type="date" class="form-control @error('data_final') is-invalid @enderror"
                                id="data_final" name="data_final" value="{{ request()->input('data_final') }}" required>
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

                <form id="report_form" action="{{ url('regiao/relatorio/estatisticagenero/pdf') }}" method="POST"
                    target="_blank" style="display: none;">
                    @csrf
                    <input type="hidden" name="data_inicial" id="report_data_inicial">
                    <input type="hidden" name="data_final" id="report_data_final">
                </form>
            </div>
        </div>
    </div>

    @if (request()->input('data_inicial') && request()->input('data_final'))
        <div class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area">
                    <!-- Conteúdo -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mt-3 text-uppercase">Ticket Médio - {{ $regiao->nome }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align: left;">{{ __('Distrito') }}</th>
                                                    <th colspan="2" style="text-align: center;">{{ __('Por Igreja') }}</th>
                                                    <th colspan="2" style="text-align: center;">{{ __('Por Membro') }}</th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: left;"></th>
                                                    <th style="text-align: center;">{{ __('Valor') }}</th>
                                                    <th style="text-align: center;">%</th>
                                                    <th style="text-align: center;">{{ __('Valor') }}</th>
                                                    <th style="text-align: center;">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lancamentos as $lancamento)
                                                    <tr>
                                                        <td>{{ $lancamento->distrito }}</td>

                                                        <!-- Itera sobre os itens do distrito -->
                                                        @foreach ($lancamento->items as $item)
                                                            <td style="text-align: center;">
                                                                {{ number_format($item->ticket_medio_igreja, 2) }}
                                                                <!-- Ticket médio por igreja -->
                                                            </td>
                                                            <td style="text-align: center;">
                                                                {{ number_format($item->percentual, 2) }}%
                                                                <!-- Percentual por igreja -->
                                                            </td>
                                                            <td style="text-align: center;">
                                                                {{ number_format($item->ticket_medio_membro, 2) }}
                                                                <!-- Ticket médio por membro -->
                                                            </td>
                                                            <td style="text-align: center;">
                                                                {{ number_format($item->percentual, 2) }}%
                                                                <!-- Percentual por membro -->
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th style="text-align: left;">{{ __('Total Geral') }}</th>
                                                    <th style="text-align: center;">
                                                        {{ number_format($lancamentos->sum('ticket_medio_igreja'), 2) }}
                                                    </th>
                                                    <th style="text-align: center;">100%</th>
                                                    <th style="text-align: center;">
                                                        {{ number_format($lancamentos->sum('ticket_medio_membro'), 2) }}
                                                    </th>
                                                    <th style="text-align: center;">100%</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                        <button class="btn btn-success btn-rounded" onclick="exportRepotirtToExcel();"><i
                                    class="fa fa-file-excel" aria-hidden="true"></i> {{ __('Exportar') }}</button>
                        </div>
                    </div>
                    <!-- Fim do Conteúdo -->
                </div>
            </div>
        </div>
    @endif

@section('extras-scripts')
    <script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
    <script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
    <script src="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker(window.IMW_SELECTPICKER_OPTIONS || {});

            $('#btn_relatorio').on('click', function(event) {
                var dataInicial = $('#data_inicial').val();
                var dataFinal = $('#data_final').val();

                if (!dataInicial || !dataFinal) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                } else {
                    $('#report_distrito').val(distrito);
                    $('#report_data_inicial').val(dataInicial);
                    $('#report_data_final').val(dataFinal);
                    $('#report_tipo').val(tipo);
                    $('#report_form').submit();
                }
            });

            $('#filter_form').submit(function(event) {
                var dataInicial = $('#data_inicial').val();
                var dataFinal = $('#data_final').val();

                if (!dataInicial || !dataFinal) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                }
            });
        });
    </script>
@endsection
@endsection
