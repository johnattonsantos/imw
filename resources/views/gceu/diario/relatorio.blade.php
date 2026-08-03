@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'GCEU', 'url' => '/gceu/lista', 'active' => false],
        ['text' => 'Diário', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/searchbuilder/1.8.2/css/searchBuilder.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/datetime/1.5.5/css/dataTables.dateTime.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />

    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }
        .cursor-pointer{
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    @include('extras.alerts')
    <!-- TABELA -->
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Diário dos GCEUs da Igreja:') }} <u id="instituicao">{{ $instituicao }}</u></h4>
                    </div>
                </div>
            </div>

            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="row">
                        <div class="mb-3 col-lg-4 col-md-6 col-sm-12" id="filtros_data">
                            <label class="control-label">{{ __('*Data:') }}</label>
                            <input type="date" class="form-control @error('dt-gceu') is-invalid @enderror" id="dt-gceu" name="dt_gceu" value="{{ request()->input('dt_gceu') }}" required placeholder="{{ __('ex: 31/12/2000') }}">
                        </div>
                        <div class="mb-3 col-lg-5 col-md-6 col-sm-12">
                            <label class="control-label">{{ __('GCEU:') }}</label>
                            <select id="gceu_id" name="gceu_id" class="form-control @error('gceu_id') is-invalid @enderror">
                                <option value="" {{ request()->input('gceu_id') == '' ? 'selected' : '' }}>{{ __('TODOS') }}</option>
                                @foreach($Gceus as $gceu)
                                    <option value="{{ $gceu->id }}" {{ request()->input('gceu_id') == $gceu->id ? 'selected' : '' }}>{{ $gceu->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-2 col-md-6 col-sm-12" style="margin-top: 30px;">
                            <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar GCEU') }}" class="btn btn-primary btn">
                                <x-bx-search /> {{ __('Buscar') }}
                            </button>
                        </div>
                    </div>
                </form>
                @if(request()->input('dt_gceu'))
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('Relatório diário de presença/falta do GCEU:') }} </h4>
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">&nbsp;</div>
                            <div class="row">


                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="diarios">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('GCEU') }}</th>
                                                <th>{{ __('NOME') }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($membros as $key => $membro)
                                            <tr>
                                                <td>{{ $key += 1 }}</td>
                                                 <td>{{ $membro->gceu_nome }}</td>
                                                <td>{{ $membro->nome }}</td>
                                                <td>
                                                    @if($membro->presenca === 0)
                                                        <i class="fas fa-times-circle" style="color: red;"></i>  Faltou
                                                    @elseif($membro->presenca === 1)
                                                        <i class="fas fa-check-circle"  style="color: green;"></i> Presente
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        @isset($key)
                                        <tfoot>
                                        <tr>
                                            <td>{{ $key }}</td>
                                            <td colspan="6"></td>
                                            </tr>
                                        </tfoot>
                                        @endif
                                    </table>
                                </div>



                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
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
    <script>
        new DataTable('#diarios', {
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
                extend: 'pdf',
                className: 'btn btn-primary btn-rounded',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'PDF',
                title: "{{ $titulo }}",

                customize: function (doc) {
                            doc.content.splice(0,1);
                            var now = new Date();
                            var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();
                            //doc.pageMargins = [20,50,20,30];
                            doc.defaultStyle.fontSize = 9;
                            doc.styles.tableHeader.fontSize = 9;


                            const hoje = new Date();
                            const dataFormatada = hoje.toLocaleDateString('pt-BR');
                            const horaFormatada = hoje.toLocaleTimeString('pt-BR');
                            const dataHoraFormatada = `${dataFormatada} ${horaFormatada}`;
                            doc['header']=(function() {
                                return {
                                    columns: [

                                        {
                                            alignment: 'left',
                                            italics: false,
                                            text: `{{ $titulo }}`,
                                            fontSize: 14,
                                            margin: [10,0]
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
                }]
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
