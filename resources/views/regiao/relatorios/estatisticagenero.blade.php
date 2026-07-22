@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'Estatística por Gênero', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
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
                    <h4>{{ __('Estatística por Gênero') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">{{ __('* Distrito:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control" id="distrito" name="distrito" required>
                            <option value="">{{ __('Selecione') }}</option>
                            <option value="all" {{ request()->input('distrito') == 'all' ? 'selected' : '' }}>{{ __('Todos') }}</option>
                            @foreach($distritos as $distrito)
                                <option value="{{ $distrito->id }}" {{ request()->input('distrito') == $distrito->id ? 'selected' : '' }}>{{ $distrito->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-4" id="filtros_data_inicial">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">{{ __('* Data Inicial:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="date" class="form-control @error('data_inicial') is-invalid @enderror" id="data_inicial" name="data_inicial" value="{{ request()->input('data_inicial') }}" required>
                    </div>
                </div>
                <div class="form-group row mb-4" id="filtros_data_final">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">{{ __('* Data Final:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="date" class="form-control @error('data_final') is-invalid @enderror" id="data_final" name="data_final" value="{{ request()->input('data_final') }}" required>
                    </div>
                </div>
                <div class="form-group row mb-4" id="filtros_congregados">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">{{ __('* Incluir Congregados:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control" id="tipo" name="tipo">
                            <option value="">{{ __('Selecione') }}</option>
                            <option value="C" {{ request()->input('tipo') == 'C' ? 'selected' : '' }}>{{ __('Sim') }}</option>
                            <option value="M" {{ request()->input('tipo') == 'M' ? 'selected' : '' }}>{{ __('Não') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-4">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-6">
                        <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn">
                            <x-bx-search /> {{ __('Buscar') }}
                        </button>
                        <button id="btn_relatorio" type="button" class="btn btn-secondary">
                            <i class="fa fa-file-pdf"></i> {{ __('Relatório') }}
                        </button>
                    </div>
                </div>
            </form>

            <form id="report_form" action="{{ url('regiao/relatorio/estatisticagenero/pdf') }}" method="POST" target="_blank" style="display: none;">
                @csrf
                <input type="hidden" name="distrito" id="report_distrito">
                <input type="hidden" name="data_inicial" id="report_data_inicial">
                <input type="hidden" name="data_final" id="report_data_final">
                <input type="hidden" name="tipo" id="report_tipo">
            </form>
        </div>
    </div>
</div>

@if(request()->input('data_inicial') && request()->input('data_final'))
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mt-3">QUANTIDADE DE MEMBROS - {{ optional($instituicao)->nome ?? $regiao->nome }}</h6>
                            <div class="table-responsive">
                                <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th colspan="2" width="200px" style="text-align: left"></th>
                                            <th colspan="2" style="text-align: center">{{ __('TOTAL MASCULINO') }}</th>
                                            <th colspan="2" style="text-align: center">{{ __('TOTAL FEMININO') }}</th>
                                            <th colspan="2" style="text-align: center">{{ __('TOTAL') }}</th>
                                        </tr>
                                        <tr>
                                            <th width="40px" style="text-align: left">{{ __('DISTRITO') }}</th>
                                            <th width="200px" style="text-align: left">{{ __('IGREJA') }}</th>
                                            <th width="140px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_inicial'))->format('d/m/Y') }}</th>
                                            <th width="140px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_final'))->format('d/m/Y') }}</th>
                                            <th width="140px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_inicial'))->format('d/m/Y') }}</th>
                                            <th width="140px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_final'))->format('d/m/Y') }}</th>
                                            <th width="140px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_inicial'))->format('d/m/Y') }}</th>
                                            <th width="140px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_final'))->format('d/m/Y') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $totalMasculinoX = 0;
                                        $totalMasculinoY = 0;
                                        $totalFemininoX = 0;
                                        $totalFemininoY = 0;
                                        $totalX = 0;
                                        $totalY = 0;
                                        @endphp
                                        @foreach($lancamentos as $lancamento)
                                        <tr>
                                            <td style="text-align: left; min-width: 100px;">{{ $lancamento->distrito }}</td>
                                            <td style="text-align: left; min-width: 100px;">{{ $lancamento->nome }}</td>
                                            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_masculino_x }}</td>
                                            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_masculino_y }}</td>
                                            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_feminino_x }}</td>
                                            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_feminino_y }}</td>
                                            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_x }}</td>
                                            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_y }}</td>
                                        </tr>
                                        @php
                                        $totalMasculinoX += $lancamento->total_masculino_x;
                                        $totalMasculinoY += $lancamento->total_masculino_y;
                                        $totalFemininoX += $lancamento->total_feminino_x;
                                        $totalFemininoY += $lancamento->total_feminino_y;
                                        $totalX += $lancamento->total_x;
                                        $totalY += $lancamento->total_y;
                                        @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align: left;">{{ __('Total Geral') }}</th>
                                            <th style="text-align: center;">{{ $totalMasculinoX }}</th>
                                            <th style="text-align: center;">{{ $totalMasculinoY }}</th>
                                            <th style="text-align: center;">{{ $totalFemininoX }}</th>
                                            <th style="text-align: center;">{{ $totalFemininoY }}</th>
                                            <th style="text-align: center;">{{ $totalX }}</th>
                                            <th style="text-align: center;">{{ $totalY }}</th>
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
                    <button class="btn btn-success btn-rounded" onclick="exportReportToExcel();"><i class="fa fa-file-excel" aria-hidden="true"></i> {{ __('Exportar') }}</button>
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
            var distrito = $('#distrito').val();
            var dataInicial = $('#data_inicial').val();
            var dataFinal = $('#data_final').val();
            var tipo = $('#tipo').val();

            if (!dataInicial || !dataFinal || !tipo || !distrito) {
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
            var distrito = $('#distrito').val();
            var dataInicial = $('#data_inicial').val();
            var dataFinal = $('#data_final').val();
            var tipo = $('#tipo').val();

            if (!dataInicial || !dataFinal || !tipo || !distrito) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos.');
            }
        });
    });
</script>
@endsection
@endsection
