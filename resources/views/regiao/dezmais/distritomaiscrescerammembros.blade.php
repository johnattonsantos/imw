@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Totalização', 'url' => '#', 'active' => false],
        ['text' => '10+ Distritos em Números de Membros', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
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
                        <h4>10+ Distritos em Números de Membros- {{ $regiao->nome }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">


                    <div class="form-group row mb-4" id="filtros_data_inicial">
                        <div class="col-lg-3 text-right">
                            <label class="control-label">* Data Inicial:</label>
                        </div>
                        <div class="col-lg-3">
                            <input type="date" class="form-control @error('data_inicial') is-invalid @enderror"
                                id="data_inicial" name="data_inicial" value="{{ request()->input('data_inicial') }}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row mb-4" id="filtros_data_final">
                        <div class="col-lg-3 text-right">
                            <label class="control-label">* Data Final:</label>
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
                                title="Buscar dados do Relatório" class="btn btn-primary btn">
                                <x-bx-search /> Buscar
                            </button>
                        </div>
                    </div>
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
                                    <h6 class="mt-3 text-uppercase">10+ Distritos em Números de Membros -
                                        {{ $regiao->nome }}</h6>
                                    <div class="table-responsive">
                                        @php
                                            $totalQuantidade = $lancamentos->sum('total');
                                            $totalPercentual = $lancamentos->sum('percentual');
                                        @endphp
                                        <table class="table table-striped display nowrap"
                                            id="distritos-mais-cresceram-membros"
                                            style="font-size: 90%; margin-top: 15px; width: 100%;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align: left;">Distrito</th>
                                                    <th style="text-align: center;">Quantide de Membros Recebidos</th>
                                                    <th style="text-align: center;">Percentual</th>
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
                                                    <th style="text-align: left;">Total Geral</th>
                                                    <th style="text-align: center;">{{ $totalQuantidade }}</th>
                                                    <th style="text-align: center;">
                                                        {{ number_format($totalPercentual, 2, ',', '.') }}%
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
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
    <script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
    <script src="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();

            $('#filter_form').submit(function(event) {
                var data_inicial = $('#data_inicial').val();
                var data_final = $('#data_final').val();

                if (!data_inicial || !data_final) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                }
            });

            if ($('#distritos-mais-cresceram-membros').length) {
                new DataTable('#distritos-mais-cresceram-membros', {
                    scrollX: true,
                    layout: {
                        topStart: {
                            buttons: [
                                'pageLength',
                                {
                                    extend: 'excel',
                                    className: 'btn btn-primary btn-rounded',
                                    text: '<i class="fas fa-file-excel"></i> Excel',
                                    titleAttr: 'Excel',
                                    title: 'IMW - 10+ DISTRITOS EM NÚMEROS DE MEMBROS'
                                },
                                {
                                    extend: 'pdfHtml5',
                                    className: 'btn btn-primary btn-rounded',
                                    text: '<i class="fas fa-file-pdf"></i> PDF',
                                    titleAttr: 'PDF',
                                    title: 'IMW - 10+ DISTRITOS EM NÚMEROS DE MEMBROS',
                                    customize: function(doc) {
                                        doc.content.splice(0, 1);
                                        doc.pageMargins = [20, 50, 20, 30];
                                        doc.defaultStyle.fontSize = 8;
                                        doc.styles.tableHeader.fontSize = 8;

                                        const hoje = new Date();
                                        const dataFormatada = hoje.toLocaleDateString('pt-BR');
                                        const horaFormatada = hoje.toLocaleTimeString('pt-BR');
                                        const dataHoraFormatada = `${dataFormatada} ${horaFormatada}`;

                                        doc.header = function() {
                                            return {
                                                columns: [{
                                                    alignment: 'center',
                                                    italics: false,
                                                    text: 'IMW - 10+ DISTRITOS EM NÚMEROS DE MEMBROS',
                                                    fontSize: 14
                                                }],
                                                margin: [20, 20, 0, 0]
                                            };
                                        };

                                        var numColumns = doc.content[0].table.body[0].length;
                                        doc.content[0].table.widths = Array(numColumns).fill('*');

                                        doc.footer = function(page, pages) {
                                            return {
                                                columns: [{
                                                        alignment: 'left',
                                                        text: ['Criado em: ', {
                                                            text: dataHoraFormatada
                                                        }]
                                                    },
                                                    {
                                                        alignment: 'right',
                                                        text: ['Página ', {
                                                            text: page.toString()
                                                        }, ' de ', {
                                                            text: pages.toString()
                                                        }]
                                                    }
                                                ],
                                                margin: 20
                                            };
                                        };

                                        var objLayout = {};
                                        objLayout.hLineWidth = function() {
                                            return .5;
                                        };
                                        objLayout.vLineWidth = function() {
                                            return .5;
                                        };
                                        objLayout.hLineColor = function() {
                                            return '#aaa';
                                        };
                                        objLayout.vLineColor = function() {
                                            return '#aaa';
                                        };
                                        objLayout.paddingLeft = function() {
                                            return 4;
                                        };
                                        objLayout.paddingRight = function() {
                                            return 4;
                                        };
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
                        emptyTable: 'Nenhum registro encontrado',
                        info: 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
                        infoEmpty: 'Mostrando 0 até 0 de 0 registros',
                        infoFiltered: '(filtrado de _MAX_ registros no total)',
                        lengthMenu: 'Mostrar _MENU_ registros',
                        loadingRecords: 'Carregando...',
                        processing: 'Processando...',
                        search: 'Pesquisar:',
                        zeroRecords: 'Nenhum registro encontrado',
                        paginate: {
                            first: 'Primeiro',
                            last: 'Último',
                            next: 'Próximo',
                            previous: 'Anterior'
                        }
                    }
                });
            }
        });
    </script>
@endsection
