@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'Financeiro por categoria', 'url' => '#', 'active' => true],
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
                    <h4>{{ __('Relatório Financeiro por Categoria') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="row col-md-12">
                    <div class="form-group mb-4 col-md-3">
                        <div>
                            <label class="control-label">{{ __('* Categoria:') }}</label>
                        </div>
                        <div>
                            <select class="form-control" id="categoria" name="categoria" required>
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach($categorias as $item)
                                    <option value="{{ $item->id }}" {{ request()->input('categoria') == $item->id ? 'selected' : '' }}>{{ $item->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-4 col-md-3" id="filtros_data">
                        <div>
                            <label class="control-label">{{ __('* Data Inicial:') }}</label>
                        </div>
                        <div>
                            <input type="date" class="form-control @error('dt_inicial') is-invalid @enderror" id="dt_inicial" name="dt_inicial" value="{{ request()->input('dt_inicial') }}" required>
                        </div>
                    </div>
                    <div class="for-group mb-4 col-md-3">
                        <div>
                            <label class="control-label">{{ __('* Data Final:') }}</label>
                        </div>
                        <div>
                            <input type="date" class="form-control @error('dt_final') is-invalid @enderror" id="dt_final" name="dt_final" value="{{ request()->input('dt_final') }}" required>
                        </div>
                    </div>
                    <div class="form-group mb-1 col-md-3">
                        <div class="col-lg-2"></div>
                        <div>
                            <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}"  style="margin-top: 30px;" class="btn btn-primary">
                                <x-bx-search /> {{ __('Buscar') }}
                            </button>
                        </div>
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
                    <div class="row">
                        <div class="col-12 exibir-excel">
                            <h4 class="mt-3">{{ $titulo }}</h4>
                             <table class="table table-bordered table-striped table-hover mb-4 display nowrap">
                                <thead> 
                                    <tr>
                                        <th>{{ __('DISTRITO') }}</th>
                                        <th>{{ __('IGREJA') }}</th>
                                        <th>{{ __('CATEGORIA') }}</th>
                                        <th>{{ __('VALOR') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($dados['dados'])
                                        @foreach($dados['dados'] as $dado)
                                            @foreach($dado->igrejas as $igreja)
                                            <tr>
                                                <td>{{ $dado->distrito->distrito }}</td>
                                                <td>{{ $igreja->igreja }}</td>
                                                <td>{{ $igreja->nome }}</td>
                                                <td>{{ number_format($igreja->valor, 2, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td><b>{{ $dado->distrito->distrito }} Total</b></td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td><b>{{ number_format($dado->distrito->valor, 2, ',', '.') }}</b></td>
                                            </tr>
                                        @endforeach   
                                        <tr>
                                            <td><b>{{ __('Total Geral') }}</b></td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td><b>{{ number_format($dados['TotalGeral']->valor, 2, ',', '.') }}</b></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ __('Nenhum registro encontrado') }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endif                                 
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 exibir-front">
                            <h4 class="mt-3">{{ $titulo }}</h4>
                             <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="financeiro-por-categoria">
                                <thead>
                                    <tr>
                                        <th>{{ __('DISTRITO') }}</th>
                                        <th>{{ __('IGREJA') }}</th>
                                        <th>{{ __('CATEGORIA') }}</th>
                                        <th >{{ __('VALOR') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($dados['dados'])
                                        @foreach($dados['dados'] as $dado)
                                            @foreach($dado->igrejas as $igreja)
                                            <tr>
                                                <td>{{ $dado->distrito->distrito }}</td>
                                                <td>{{ $igreja->igreja }}</td>
                                                <td>{{ $igreja->nome }}</td>
                                                <td>{{ number_format($igreja->valor, 2, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td><b>{{ $dado->distrito->distrito }} Total</b></td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td><b>{{ number_format($dado->distrito->valor, 2, ',', '.') }}</b></td>
                                            </tr>
                                        @endforeach  
                                            <tr>
                                                <td><b>{{ __('Total Geral') }}</b></td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td><b>{{ number_format($dados['TotalGeral']->valor, 2, ',', '.') }}</b></td>
                                            </tr>
                                    @else
                                        <tr>
                                            <td>{{ __('Nenhum registro encontrado') }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endif                                      
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fim do Conteúdo -->
        </div>
    </div>
</div>
@endif
@endsection
@section('extras-scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
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
    <script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
    <script>
        $('.exibir-front').show();
        $('.exibir-excel').hide()
    var titulo = $('#titulo').val();
    var data_inicial = $('#data_inicial').val();
    var data_final = $('#data_final').val();
    $('#btn_buscar').click(function () {
        $('#filter_form').removeAttr('target');
    })
    
    $('#btn_relatorio').click(function () {
        $('#filter_form').attr('target', '_blank');
    })

    new DataTable('#financeiro-por-categoria', {
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
                    title: `{{ $titulo }}`,
                    action: function (e, dt, button, config) {
                        exportReportToExcel();
                    }
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
    </script>

@endsection