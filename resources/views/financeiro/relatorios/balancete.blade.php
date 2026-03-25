@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Relatórios - Financeiro', 'url' => '#', 'active' => false],
        ['text' => 'Balancete', 'url' => '#', 'active' => true],
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
                    <h4>Relatório Balancete</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4" id="filtros_data">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">* Período (Inicial e Final):</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control @error('dt_inicial') is-invalid @enderror" id="dt_inicial" name="dt_inicial" value="{{ request()->input('dt_inicial') }}" placeholder="mm/yyyy" required>
                    </div>
                
                    <div class="col-lg-3">
                        <input type="text" class="form-control @error('dt_final') is-invalid @enderror" id="dt_final" name="dt_final" value="{{ request()->input('dt_final') }}" placeholder="mm/yyyy" required>
                    </div>
                </div>

                {{-- Congregação --}}
                <div class="form-group row mb-4">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">Caixa:</label>
                    </div>
                    <div class="col-lg-6">
                        <select id="caixa_id" name="caixa_id" class="form-control @error('caixa_id') is-invalid @enderror">
                            <option value="all" {{ request()->input('caixa_id') == '99' ? 'selected' : '' }}>Todos
                            </option>
                            @foreach ($caixasSelect as $cx)
                            <option value="{{ $cx->id }}" {{ request()->input('caixa_id') == $cx->id ? 'selected' : '' }}>
                                {{ $cx->descricao }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-4">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-6">
                        <button id="btn_buscar" type="submit" name="action" value="buscar" title="Buscar dados do Relatório" class="btn btn-primary btn">
                            <x-bx-search /> Buscar
                        </button>
                        <button id="btn_relatorio" type="button" name="action" value="relatorio" title="Gerar Relatório" class="btn btn-secondary btn">
                            Relatório
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(request()->input('dt_inicial'))

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
        @if($instituicao == 'NaoExiste')
            Não  existe movimento consolidado para o período escolhido
        @else
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">                            
                            <table class="table table-striped" id="tblExport" style="font-size: 90%; margin-top: 15px;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th colspan="7"  style="background-color: #fff;"><h5 class="mt-3">Discriminação de saldos por caixa</h5></th>
                                    </tr>
                                    <tr>
                                        <th>CAIXA</th>
                                        <th width="300" style="text-align: right">SALDO ANTERIOR</th>
                                        <th width="120" style="text-align: right">TOTAIS DE ENTRADAS</th>
                                        <th width="120" style="text-align: right">TOTAIS DE SAÍDAS</th>
                                        <th width="120" style="text-align: right">TRANSF. ENTRADAS</th>
                                        <th width="120" style="text-align: right">TRANSF. SAÍDAS</th>
                                        <th width="120" style="text-align: right">SALDO ATUAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $totalSaldoFinal = 0;
                                    $totalEntradas = 0;
                                    $totalSaidas = 0;
                                    $totalTransferenciasEntrada = 0;
                                    $totalTransferenciasSaida = 0;
                                    $totalSaldoAtual = 0;
                                    @endphp

                                    @foreach ($caixas as $caixa)
                                    <tr>
                                        <td style="text-align: left">{{ $caixa->caixa }}</td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . number_format($caixa->saldo_final, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . number_format($caixa->total_entradas, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . ($caixa->total_saidas > 0 ? '-' : '') . number_format(abs($caixa->total_saidas), 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . number_format($caixa->total_transferencias_entrada, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . ($caixa->total_transferencias_saida > 0 ? '-' : '') . number_format(abs($caixa->total_transferencias_saida), 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . number_format($caixa->saldo_atual, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @php
                                    $totalSaldoFinal += $caixa->saldo_final;
                                    $totalEntradas += $caixa->total_entradas;
                                    $totalSaidas += $caixa->total_saidas;
                                    $totalTransferenciasEntrada += $caixa->total_transferencias_entrada;
                                    $totalTransferenciasSaida += $caixa->total_transferencias_saida;
                                    $totalSaldoAtual += $caixa->saldo_atual;
                                    @endphp
                                    @endforeach

                                    {{-- Total de cada caixa --}}
                                    <tr>
                                        <td style="text-align: left"><strong>Total dos Caixas</strong></td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . number_format($totalSaldoFinal, 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . number_format($totalEntradas, 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . ($totalSaidas > 0 ? '-' : '') . number_format(abs($totalSaidas), 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . number_format($totalTransferenciasEntrada, 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . ($totalTransferenciasSaida > 0 ? '-' : '') . number_format(abs($totalTransferenciasSaida), 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . number_format($totalSaldoAtual, 2, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                                <tr>
                                    <td colspan="7"></td>
                                </tr>
                                <tr>
                                    <td  colspan="7">
                                        <table  width="100%">

                                            <thead class="thead-dark">
                                                <tr>
                                                    <th colspan="7"  style="background-color: #fff;"><h5 class="mt-3">Discriminação dos Lançamentos por Conta</h5></th>
                                                </tr>
                                                <tr>
                                                    <th>CONTA</th>
                                                    <th colspan="5">CAIXA</th>
                                                    <th width="120" style="text-align: right;">TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                $numerosJaExibidos = [];
                                                $somasPorNumeracao = [];
                                                @endphp

                                                {{-- Calcular a soma total para cada numeracao --}}
                                                @foreach ($lancamentos as $lancamento)
                                                @php
                                                if (!isset($somasPorNumeracao[$lancamento->numeracao])) {
                                                $somasPorNumeracao[$lancamento->numeracao] = 0;
                                                }
                                                $somasPorNumeracao[$lancamento->numeracao] += $lancamento->total;
                                                @endphp
                                                @endforeach

                                                {{-- Renderizar a tabela --}}
                                                
                                                @foreach ($lancamentos as $index => $lancamento)
                                                @if (!in_array($lancamento->numeracao, $numerosJaExibidos))
                                                    @php
                                                        $total[] = $somasPorNumeracao[$lancamento->numeracao];
                                                    @endphp
                                                    <tr>
                                                        <td style="width: 100px;">{{ $lancamento->numeracao }}</td>
                                                        <td  colspan="5" style="font-weight: bold;">
                                                            {{ $lancamento->nome }}
                                                        </td>
                                                        <td style="text-align: right; font-weight: bold;">
                                                            R$ {{ number_format($somasPorNumeracao[$lancamento->numeracao], 2, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    @php $numerosJaExibidos[] = $lancamento->numeracao; @endphp
                                                @endif
                                                @php
                                                    $total[] = $lancamento->total;
                                                @endphp
                                                <tr>
                                                    <td></td>
                                                    <td  colspan="5" style="text-align: left;">{{ $lancamento->caixa }}</td>
                                                    <td style="text-align: right;">R$ {{ number_format($lancamento->total, 2, ',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                                <!-- <tr>
                                                    <th></th>   
                                                    <th  colspan="5" style="text-align: left;">Total Conta</th>
                                                    @php
                                                        $total = isset($total) ? $total : [];
                                                    @endphp
                                                    <th style="text-align: right;">R$ {{ number_format(array_sum($total), 2, ',', '.') }}</th>
                                                </tr> -->
                                            </tbody>
                                        </table>                                        
                                    </td>
                                </tr>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <button class="btn btn-success btn-rounded" id="btnExport"><i class="fa fa-file-excel" aria-hidden="true"></i> Exportar</button>
                </div>
            </div>
            <!-- Fim do Conteúdo -->
        @endif
        </div>
    </div>
</div>
@endif

@section('extras-scripts')
<script src="{{ asset('theme/plugins/excel-export/jquery.btechco.excelexport.js') }}"></script>
<script src="{{ asset('theme/plugins/excel-export/jquery.base64.js') }}"></script>
<script>
    $(document).ready(function () {
        $("#btnExport").click(function () {
            $("#tblExport").btechco_excelexport({
                containerid: "tblExport"
               , datatype: $datatype.Table
               , filename: 'Relatorio-discriminacao-de-saldos-por-caixa-e-lancamentos-por-conta'
            });
        });
    });
</script>
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

        $('#btn_relatorio').on('click', function() {
            var dataInicial = $('#dt_inicial').val();
            var dataFinal = $('#dt_final').val();
            var caixaId = $('#caixa_id').val();

            if (!dataInicial || !dataFinal) {
                alert('Por favor, preencha os campos de data inicial e data final.');
                return;
            }

            var url = '{{ url("/financeiro/relatorio/balancete-pdf") }}' +
                      '?dt_inicial=' + encodeURIComponent(dataInicial) +
                      '&dt_final=' + encodeURIComponent(dataFinal) +
                      '&caixa_id=' + encodeURIComponent(caixaId);

            window.open(url, '_blank');
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#filter_form').submit(function(event) {
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
        });
    });
</script>
<script>
    function validarFormulario() {
        const dataInicial = document.getElementById('dt_inicial').value;
        const dataFinal = document.getElementById('dt_final').value;

        if (!dataInicial || !dataFinal) {
            alert('Por favor, preencha os campos de mês inicial e mês final.');
            return false;
        }

        return true;
    }

    document.getElementById('btn_buscar').addEventListener('click', function(event) {
        if (!validarFormulario()) {
            event.preventDefault();
        }
    });

    function gerarRelatorio() {
        if (validarFormulario()) {
            const form = document.getElementById('filter_form');
            form.action = "{{ route('financeiro.relatorio-balancete-pdf') }}";
            form.submit();
        }
    }
</script>

<script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
<script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
@endsection
@endsection
