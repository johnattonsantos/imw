@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Totalização', 'url' => '#', 'active' => false],
        ['text' => 'Total de Frentes Missionários', 'url' => '#', 'active' => true],
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
                        <h4>Total de Frentes Missionários- {{ $regiao->nome }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="form-group row mb-4">
                        <div class="col-lg-3 text-right">
                            <label class="control-label">{{ __('* Instituição:') }}</label>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-control" id="instituicao" name="instituicao" required>
                                <option value="">{{ __('Selecione') }}</option>
                                <option value="3" {{ request()->input('instituicao') == 3 ? 'selected' : '' }}>{{ __('Região') }}</option>
                                <option value="2" {{ request()->input('instituicao') == 2 ? 'selected' : '' }}>{{ __('Distrito') }}</option>
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

                <form id="report_form" action="{{ route('regiao.totalizacao.totalfrentemissionaria-pdf') }}" method="POST"
                    target="_blank" style="display: none;">
                    @csrf
                    <input type="hidden" name="instituicao" id="report_instituicao">
                </form>
            </div>
        </div>
    </div>

  @if (request()->input('instituicao'))
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <!-- Conteúdo -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mt-3 text-uppercase">Total de Frentes Missionários -
                                    {{ $regiao->nome }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="text-align: left;">{{ __('Instituição') }}</th>
                                                <th style="text-align: center;">{{ __('Quantidade de Frentes') }}</th>
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
                <div class="row">
                    <div class="col-12 text-center">
                        <button class="btn btn-success btn-rounded" onclick="exportReportToExcel();"><i
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
                var instituicao = $('#instituicao').val();

                if (!instituicao) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                } else {
                    $('#report_instituicao').val(instituicao);
                    $('#report_form').submit();
                }
            });

            $('#filter_form').submit(function(event) {
                var instituicao = $('#instituicao').val();


                if (!instituicao) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                }
            });
        });
    </script>
@endsection
@endsection
