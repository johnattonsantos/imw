@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Estatísticas', 'url' => '#', 'active' => false],
        ['text' => 'Estatística Total Membros', 'url' => '#', 'active' => true],
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
                        <h4>{{ __('Estatística Total Membros') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="form-group row mb-4">
                        <div class="col-lg-3 text-right">
                            <label class="control-label">{{ __('* Região:') }}</label>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-control" id="regiao" name="regiao" required>
                                <option value="">{{ __('Selecione') }}</option>
                                <option {{ request()->input('regiao') == 'all' ? 'selected' : '' }} value="all">{{ __('Todos') }}
                                </option>
                                @foreach ($regioes as $regiao)
                                    <option value="{{ $regiao->id }}"
                                        {{ request()->input('regiao') == $regiao->id ? 'selected' : '' }}>
                                        {{ $regiao->nome }}</option>
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

                <form id="report_form" action="{{ url('regiao/relatorio/estatisticatotalmembros/pdf') }}" method="POST"
                    target="_blank" style="display: none;">
                    @csrf
                    <input type="hidden" name="regiao" id="report_regiao">
                </form>
            </div>
        </div>
    </div>

    @if (request()->input('regiao'))
        <div class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area">
                    <!-- Conteúdo -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mt-3">{{ __('QUANTIDADE DE MEMBROS -') }}</h6>
                                    <div class="table-responsive">
                                      <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align: left;">{{ __('Instituição') }}</th>
                                                    <th style="text-align: center;">{{ __('Total') }}</th>
                                                    <th style="text-align: center;">{{ __('Percentual') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lancamentos as $lancamento)
                                                    <tr>
                                                        <td>{{ $lancamento->instituicao }}</td>
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
                var regiao = $('#regiao').val();


                if (!regiao) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                } else {
                    $('#report_regiao').val(regiao);
                }
            });

            $('#filter_form').submit(function(event) {
                var regiao = $('#regiao').val();


                if (!regiao) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                }
            });
        });
    </script>
@endsection
@endsection
