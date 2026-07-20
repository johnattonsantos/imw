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
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/searchbuilder/1.8.2/css/searchBuilder.dataTables.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/datetime/1.5.5/css/dataTables.dateTime.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Relatório Balancete') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4" id="filtros_data">
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

                {{-- Congregação --}}
                <div class="form-group row mb-4">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">{{ __('Igrejas:') }}</label>
                    </div>
                    <div class="col-lg-6">
                        <select id="instituicao_id" name="instituicao_id" class="form-control @error('instituicao_id') is-invalid @enderror">
                            <option value="0" {{ request()->input('instituicao_id') == 'all' ? 'selected' : '' }}>{{ __('Todas') }}
                            </option>
                            @foreach ($igrejas as $igreja)
                            <option value="{{ $igreja->id }}" {{ request()->input('instituicao_id') == $igreja->id ? 'selected' : '' }}>
                                {{ $igreja->descricao }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-4">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-6">
                        <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn">
                            <x-bx-search /> {{ __('Buscar') }}
                        </button>
                        <!-- <button id="btn_relatorio" type="button" name="action" value="relatorio" title="{{ __('Gerar Relatório') }}" class="btn btn-secondary btn">
                            {{ __('Relatório') }}
                        </button> -->
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
                <!-- Conteúdo -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="col-12 mt-3">
                            <h5>{{ $titulo }}</h5>
                        </div>
                        <div class="col-12">
                            <table class="table" id="balacente-regiao">
                                <thead>
                                    <tr>
                                        <th>
                                            {{ __('Tipo') }}
                                        </th>
                                        <th>{{ __('Valor') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                            <td style="font-weight: bold;">{{ __('Saldo Inicial') }}</td>
                                            <td style="text-align: right; font-weight: bold;">
                                                {{ 'R$ ' . number_format($caixas[0]->saldo, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">{{ __('Total de entradas') }}</td>
                                            <td style="text-align: right; font-weight: bold;">
                                                {{ 'R$ ' . number_format($caixas[1]->saldo, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">{{ __('Total de saídas') }}</td>
                                            <td style="text-align: right; font-weight: bold;">
                                                {{ 'R$ ' . number_format($caixas[2]->saldo, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">{{ __('Total de transferências entradas') }}</td>
                                            <td style="text-align: right; font-weight: bold;">
                                                {{ 'R$ ' . number_format($caixas[3]->saldo, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">{{ __('Total de transferências saídas') }}</td>
                                            <td style="text-align: right; font-weight: bold;">
                                                {{ 'R$ ' . number_format($caixas[4]->saldo, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">{{ __('Saldo atual') }}</td>
                                            <td style="text-align: right; font-weight: bold;">
                                                {{ 'R$ ' . number_format($caixas[5]->saldo, 2, ',', '.') }}
                                            </td>
                                        </tr>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-12 text-center">
                        <button class="btn btn-success btn-rounded" onclick="exportReportToExcel();"><i class="fa fa-file-excel" aria-hidden="true"></i> {{ __('Exportar') }}</button>
                    </div>
                </div> -->
                <!-- Fim do Conteúdo -->
            </div>
        </div>
    </div>
@endif

@section('extras-scripts')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/searchbuilder/1.8.2/js/dataTables.searchBuilder.js"></script>
<script src="https://cdn.datatables.net/searchbuilder/1.8.2/js/searchBuilder.dataTables.js"></script>
<script src="https://cdn.datatables.net/datetime/1.5.5/js/dataTables.dateTime.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>
<script>
    new DataTable('#balacente-regiao', {
        scrollX: true,
        scrollY: 400,
        scrollCollapse: true,
        layout: {
            //top1: 'searchBuilder',
            topStart: {
            buttons: [
                'pageLength',
                {
                  extend: 'excel',
                  className: 'btn btn-primary btn-rounded',
                  text: '<i class="fas fa-file-excel"></i> Excel',
                  titleAttr: 'Excel',
                  title: "{{ $titulo }}"
                },
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-primary btn-rounded',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    titleAttr: 'PDF',
                    title: `{{ $titulo }}`,
                    customize: function (doc) {
                        doc.content.splice(0,1);
                        var now = new Date();
                        var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();
                        doc.pageMargins = [20,50,20,30];
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 8;

                        const hoje = new Date();
                        const dataFormatada = hoje.toLocaleDateString('pt-BR');
                        const horaFormatada = hoje.toLocaleTimeString('pt-BR');
                        const dataHoraFormatada = `${dataFormatada} ${horaFormatada}`;
                        doc['header']=(function() {
                            return {
                                columns: [

                                    {
                                        alignment: 'center',
                                        italics: false,
                                        text: `{{ $titulo }}`,
                                        fontSize: 14,
                                        //margin: [10,0]
                                    },
                                    // {
                                    //     alignment: 'right',
                                    //     fontSize: 14,
                                    //     text: ``
                                    // }
                                ],
                                margin: [20,20,0,0]
                            }
                        });

                        var numColumns = doc.content[0].table.body[0].length;
                        doc.content[0].table.widths = Array(numColumns).fill('*');

                        doc['footer']=(function(page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'left',
                                        text: ['Criado em: ', { text: dataHoraFormatada }]
                                    },
                                    {
                                        alignment: 'right',
                                        text: ['Página ', { text: page.toString() },  ' de ', { text: pages.toString() }]
                                    }
                                ],
                                margin: 20
                            }
                        });

                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) { return .5; };
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        objLayout['paddingLeft'] = function(i) { return 4; };
                        objLayout['paddingRight'] = function(i) { return 4; };
                        doc.content[0].layout = objLayout;
                    },
                    pageSize: 'LEGAL'
                }
                ]
            },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        language: {
        url:"https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json"
        }
    });
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
