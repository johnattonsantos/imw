@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Distritais', 'url' => '#', 'active' => false],
    ['text' => 'Orçamento', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Relatório Orçamento') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4" id="filtros_data">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">{{ __('* Ano:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control @error('dtano') is-invalid @enderror" id="dtano" name="dtano" value="{{ request()->input('dtano') }}" placeholder="{{ __('Exemplo: 2024') }}" required>
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
            <form id="report_form" action="{{ url('distrito/relatorio/orcamento/pdf') }}" method="POST" target="_blank" style="display: none;">
                @csrf
                <input type="hidden" name="dtano" id="report_dtano">
                <input type="hidden" name="igrejas" id="report_igrejas">
            </form>
        </div>
    </div>
</div>

@if(request()->input('dtano'))
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mt-3">ORÇAMENTO - {{ session('session_perfil')->instituicao_nome }}</h6>
                            <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="300" style="text-align: left">{{ __('IGREJA') }}</th>
                                        <th width="50" style="text-align: right">{{ __('JAN') }}</th>
                                        <th width="50" style="text-align: right">{{ __('FEV') }}</th>
                                        <th width="50" style="text-align: right">{{ __('MAR') }}</th>
                                        <th width="50" style="text-align: right">{{ __('ABR') }}</th>
                                        <th width="50" style="text-align: right">{{ __('MAI') }}</th>
                                        <th width="50" style="text-align: right">{{ __('JUN') }}</th>
                                        <th width="50" style="text-align: right">{{ __('JUL') }}</th>
                                        <th width="50" style="text-align: right">{{ __('AGO') }}</th>
                                        <th width="50" style="text-align: right">{{ __('SET') }}</th>
                                        <th width="50" style="text-align: right">{{ __('OUT') }}</th>
                                        <th width="50" style="text-align: right">{{ __('NOV') }}</th>
                                        <th width="50" style="text-align: right">{{ __('DEZ') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $totalJaneiro = 0;
                                    $totalFevereiro = 0;
                                    $totalMarco = 0;
                                    $totalAbril = 0;
                                    $totalMaio = 0;
                                    $totalJunho = 0;
                                    $totalJulho = 0;
                                    $totalAgosto = 0;
                                    $totalSetembro = 0;
                                    $totalOutubro = 0;
                                    $totalNovembro = 0;
                                    $totalDezembro = 0;
                                    @endphp
                                    @foreach($lancamentos as $lancamento)
                                    <tr>
                                        <td>{{ $lancamento->instituicao_nome }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->janeiro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->fevereiro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->marco, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->abril, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->maio, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->junho, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->julho, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->agosto, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->setembro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->outubro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->novembro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->dezembro, 2, ',', '.') }}</td>
                                    </tr>
                                    @php
                                    $totalJaneiro += $lancamento->janeiro;
                                    $totalFevereiro += $lancamento->fevereiro;
                                    $totalMarco += $lancamento->marco;
                                    $totalAbril += $lancamento->abril;
                                    $totalMaio += $lancamento->maio;
                                    $totalJunho += $lancamento->junho;
                                    $totalJulho += $lancamento->julho;
                                    $totalAgosto += $lancamento->agosto;
                                    $totalSetembro += $lancamento->setembro;
                                    $totalOutubro += $lancamento->outubro;
                                    $totalNovembro += $lancamento->novembro;
                                    $totalDezembro += $lancamento->dezembro;
                                    @endphp
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td>{{ __('Totais') }}</td>
                                        <td style="text-align: right">{{ number_format($totalJaneiro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalFevereiro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalMarco, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalAbril, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalMaio, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalJunho, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalJulho, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalAgosto, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalSetembro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalOutubro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalNovembro, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($totalDezembro, 2, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>

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
    jQuery(function($) {
        $.datepicker.regional['pt-BR'] = {
            closeText: 'Aplicar',
            prevText: '&#x3c;Anterior',
            nextText: 'Pr&oacute;ximo&#x3e;',
            currentText: 'Hoje',
            monthNames: ['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ],
            monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
                'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
            ],
            dayNames: ['Domingo', 'Segunda-feira', 'Ter&ccedil;a-feira', 'Quarta-feira', 'Quinta-feira',
                'Sexta-feira', 'Sabado'
            ],
            dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            dayNamesMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            weekHeader: 'Sm',
            dateFormat: 'yy',
            firstDay: 0,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
    });

    $(document).ready(function() {
        $('.selectpicker').selectpicker(window.IMW_SELECTPICKER_OPTIONS || {});

        // Inicializar o Datepicker
        $("#dtano").datepicker({
            dateFormat: "yy", // Formato do calendário (apenas ano)
            changeYear: true, // Permitir a seleção do ano
            showButtonPanel: true,
            language: 'pt-BR', // Definir o idioma como português
            onClose: function(dateText, inst) {
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker("setDate", new Date(year, 0, 1));
            }
        }).focus(function() {
            $(".ui-datepicker-month").hide();
            $(".ui-datepicker-calendar").hide();
        });

        $('#btn_relatorio').on('click', function(event) {
            var dataAno = $('#dtano').val();

            // Verificar se a data está preenchida
            if (!dataAno) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos.');
            } else {
                // Preencher e submeter o formulário oculto
                $('#report_dtano').val(dataAno);
                $('#report_form').submit();
            }
        });

        $('#filter_form').submit(function(event) {
            var dataAno = $('#dtano').val();

            // Verificar se a data está preenchida
            if (!dataAno) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos.');
            }
        });
    });
</script>
@endsection
@endsection