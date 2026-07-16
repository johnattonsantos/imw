@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'Variação financeira', 'url' => '#', 'active' => true],
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
                    <h4>{{ __('Relatório Variação financeira') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">{{ __('* Distrito:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control" id="distrito" name="distrito" required>
                            <option value="">{{ __('Selecione') }}</option>
                            @foreach($distritos as $distrito)
                                <option value="{{ $distrito->id }}" {{ request()->input('distrito') == $distrito->id ? 'selected' : '' }}>{{ $distrito->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-4">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">{{ __('* Período (Inicial e Final):') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control @error('dt_inicial') is-invalid @enderror" id="dt_inicial" name="dt_inicial" value="{{ request()->input('dt_inicial') }}" placeholder="{{ __('mm/yyyy') }}" required>
                    </div>

                    <div class="col-lg-3">
                        <input type="text" class="form-control @error('dt_final') is-invalid @enderror" id="dt_final" name="dt_final" value="{{ request()->input('dt_final') }}" placeholder="{{ __('mm/yyyy') }}" required>
                    </div>
                </div>
                <div class="form-group row mb-4">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-6">
                        <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn">
                            <x-bx-search /> {{ __('Buscar') }}
                        </button>
                        <button id="btn_relatorio" type="button" name="action" value="relatorio" title="{{ __('Gerar Relatório') }}" class="btn btn-secondary btn">
                            {{ __('Relatório') }}
                        </button>
                    </div>
                </div>
            </form>
            <form id="report_form" action="{{ url('regiao/relatorio/variacaofinanceira/pdf') }}" method="POST" target="_blank" style="display: none;">
                @csrf
                <input type="hidden" name="distrito" id="report_distrito">
                <input type="hidden" name="dt_inicial" id="report_dtinicio">
                <input type="hidden" name="dt_final" id="report_dtfinal">
            </form>
        </div>
    </div>
</div>

@if(request()->input('dt_inicial'))
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mt-3">VARIAÇÃO FINANCEIRA - {{ $instituicao->nome }}</h5>
                            <p>Período de {{request()->input('dt_inicial')}} a {{ request()->input('dt_final')}}</p>
                            <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="200" style="text-align: left">{{ __('IGREJA') }}</th>
                                        <th width="100" style="text-align: right">{{ __('SALDO ANTERIOR') }}</th>
                                        <th width="100" style="text-align: right">{{ __('ENTRADAS') }}</th>
                                        <th width="100" style="text-align: right">{{ __('TRANSF. ENTRADAS') }}</th>
                                        <th width="100" style="text-align: right">{{ __('SAÍDAS') }}</th>
                                        <th width="100" style="text-align: right">{{ __('TRANSF. SAÍDAS') }}</th>
                                        <th width="100" style="text-align: right">{{ __('SALDO FINAL') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lancamentos as $lancamento)
                                    <tr>
                                        <td>{{ $lancamento->instituicao_nome }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->saldo_anterior, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->total_entradas, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->total_transf_entradas, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->total_saidas, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->total_transf_saidas, 2, ',', '.') }}</td>
                                        <td style="text-align: right">{{ number_format($lancamento->saldo_final, 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
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
        $("#dt_inicial").datepicker({
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

        $("#dt_final").datepicker({
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
            var distrito = $('#distrito').val();
            var dt_inicial = $('#dt_inicial').val();
            var dt_final = $('#dt_final').val();

            // Verificar se a data está preenchida
            if (!dt_inicial || !dt_final || !distrito) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos.');
            } else {
                // Preencher e submeter o formulário oculto
                $('#report_distrito').val(distrito);
                $('#report_dtinicio').val(dt_inicial);
                $('#report_dtfinal').val(dt_final);
                $('#report_form').submit();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#filter_form').submit(function(event) {
            var distrito = $('#distrito').val();
            var dataInicial = $('#dt_inicial').val();
            var dataFinal = $('#dt_final').val();

            // Converter as datas para objetos Date
            var dateInicial = new Date(dataInicial);
            var dateFinal = new Date(dataFinal);

            // Verificar se a data final é menor que a data inicial
            if (dateFinal < dateInicial) {
                // Impedir o envio do formulário
                event.preventDefault();
                // Exibir uma mensagem de erro
                alert('O mês final não pode ser menor que o mês inicial.');
            }

            // Verificar se o distrito está preenchido
            if (!distrito) {
                event.preventDefault();
                alert('Por favor, preencha o distrito.');
            }
        });
    });
</script>
<script>
    function validarFormulario() {
        const distrito = document.getElementById('distrito').value;
        const dataInicial = document.getElementById('dt_inicial').value;
        const dataFinal = document.getElementById('dt_final').value;

        if (!dataInicial || !dataFinal) {
            alert('Por favor, preencha os campos de mês inicial e mês final.');
            return false;
        }

        if (!distrito) {
            alert('Por favor, selecione um distrito.');
            return false;
        }

        return true;
    }

    document.getElementById('btn_buscar').addEventListener('click', function(event) {
        if (!validarFormulario()) {
            event.preventDefault();
        }
    });
</script>

<script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
<script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
@endsection
@endsection