@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Distritais', 'url' => '#', 'active' => false],
    ['text' => 'Saldo de Caixas', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Relatório Saldo de Caixas') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4" id="filtros_data">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">{{ __('* Mês/Ano:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control @error('dt') is-invalid @enderror" id="dt" name="dt" value="{{ request()->input('dt') }}" placeholder="{{ __('mm/yyyy') }}" required>
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
            <form id="report_form" action="{{ url('distrito/relatorio/saldodasigrejas/pdf') }}" method="POST" target="_blank" style="display: none;">
                @csrf
                <input type="hidden" name="dt" id="report_dt">
            </form>
        </div>
    </div>
</div>

@if(request()->input('dt'))
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mt-3">SALDO DE CAIXAS - {{ session('session_perfil')->instituicao_nome }}</h6>
                            <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="150" style="text-align: left">{{ __('IGREJA') }}</th>
                                        <th width="150" style="text-align: right">{{ __('SALDO CAIXA PRINCIPAL') }}</th>
                                        <th width="180" style="text-align: right">{{ __('SALDO CAIXA CONGREGAÇÕES') }}</th>
                                        <th width="150" style="text-align: right">{{ __('SALDO CAIXA SECUNDÁRIO') }}</th>
                                        <th width="150" style="text-align: right">{{ __('SALDO CAIXA BANCOS') }}</th>
                                        <th width="150" style="text-align: right">{{ __('SALDO OUTROS CAIXAS') }}</th>
                                        <th width="70" style="text-align: right">{{ __('TOTAL') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lancamentos as $lancamento)
                                    @php
                                        $saldoCxPrincipal[] = $lancamento->saldocxprincipal;
                                        $saldoCxCongregacoes[] = $lancamento->saldocxcongregacoes;
                                        $saldoCxSecundado[] = $lancamento->saldocxsecundado;
                                        $saldoCxBancos[] = $lancamento->saldocxbancos;
                                        $saldoCxOutros[] = $lancamento->saldocxoutros;
                                        $total[] = $lancamento->total;
                                    @endphp
                                    <tr>
                                        <td>{{ $lancamento->instituicao_nome }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->saldocxprincipal, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->saldocxcongregacoes, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->saldocxsecundado, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->saldocxbancos, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->saldocxoutros, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->total, 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td>{{ __('TOTAL') }}</td>
                                        <td style="text-align: right">{{ number_format(array_sum($saldoCxPrincipal), 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format(array_sum($saldoCxCongregacoes), 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format(array_sum($saldoCxSecundado), 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format(array_sum($saldoCxBancos), 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format(array_sum($saldoCxOutros), 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format(array_sum($total), 2, ',', '.') }}</td>
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
            dateFormat: 'dd/mm/yy',
            firstDay: 0,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
    });

    $(document).ready(function() {
        // Inicializar o Datepicker
        $("#dt").datepicker({
            dateFormat: "mm/yy", // Formato do calendário (mês/ano)
            changeMonth: true, // Permitir a seleção do mês
            changeYear: true, // Permitir a seleção do ano
            showButtonPanel: true,
            language: 'pt-BR', // Definir o idioma como português
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker("setDate", new Date(year, month, 1));
            }
        }).focus(function() {
            $(".ui-datepicker-calendar").hide();
        });

        $('#btn_relatorio').on('click', function(event) {
            var dt = $('#dt').val();

            // Verificar se a data está preenchida
            if (!dt) {
                event.preventDefault();
                alert('Por favor, preencha o campo obrigatório.');
            } else {
                // Preencher e submeter o formulário oculto
                $('#report_dt').val(dt);
                $('#report_form').submit();
            }
        });


        $('#filter_form').submit(function(event) {
            var dt = $('#dt').val();

            // Verificar se a data está preenchida
            if (!dt) {
                event.preventDefault();
                alert('Por favor, preencha o campo obrigatório.');
            }
        });
    });
</script>
<script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
<script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
@endsection
@endsection