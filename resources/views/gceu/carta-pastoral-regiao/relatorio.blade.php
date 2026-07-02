@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Região', 'url' => '#', 'active' => false],
        ['text' => 'GCEU', 'url' => '#', 'active' => false],
        ['text' => 'Carta Pastoral', 'url' => '#', 'active' => true],
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
    </style>
@endsection

@section('content')
    <!-- TABELA -->
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Relatório de Cartas Pastorais - Região: <u>{{ $instituicao }}</u></h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form">
                    
                    {{-- Congregação --}}
                    <div class="form-group row mb-4">
                        <div class="col-lg-4">
                            <label class="control-label">Distrito:</label>
                            <select id="regiao_id" name="distrito_id" class="form-control @error('distrito_id') is-invalid @enderror" >
                            <option value="" {{ request()->distrito_id == '' ? 'selected' : '' }}>TODOS</option>
                            @foreach($distritos as $distrito)
                                <option value="{{ $distrito->id }}" {{ request()->distrito_id == $distrito->id ? 'selected' : '' }}>{{ $distrito->distrito_nome }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="control-label">Igreja:</label>
                            <select id="igreja_id" name="igreja_id" class="form-control @error('igreja_id') is-invalid @enderror" >
                            <option value="" {{ request()->igreja_id == '' ? 'selected' : '' }}>TODAS</option>
                            @foreach($igrejas as $igreja)
                                <option value="{{ $igreja->id_igreja }}" {{ request()->igreja_id == $igreja->id_igreja ? 'selected' : '' }}>{{ $igreja->igreja_nome }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button id="btn_buscar" type="submit" name="action" value="buscar" title="Buscar dados do Relatório" class="btn btn-primary btn" style="margin-top: 30px;">
                            <x-bx-search /> Buscar 
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="widget-content widget-content-area">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-4" id="cartaPastoral">
                        <thead>
                            <tr>
                                <th>DISTRITO</th>
                                <th>IGREJA</th>
                                <th>CARTA PASTORAL</th>
                                <th>PASTOR</th>
                                <th>CRIADO EM</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartasPastorais as $cartaPastoral)
                            <tr>
                                <td>{{ $cartaPastoral->distrito_nome }}</td>
                                <td>{{ $cartaPastoral->igreja_nome }}</td>
                                <td>{{ $cartaPastoral->titulo }}</td>
                                <td>{{ $cartaPastoral->pastor }}</td>
                                <td>{{ formatDate($cartaPastoral->data_criacao) }}</td>
                                <td>@include('gceu.carta-pastoral-regiao.slice-actions-relatorio')</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="visualizarGCEUCartaPastoralModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content loadable">
                <div class="modal-body" style="min-height: 200px"></div>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.8.2/js/dataTables.searchBuilder.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.8.2/js/searchBuilder.dataTables.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.5/js/dataTables.dateTime.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="{{ asset('gceu/js/cartaPastoral.js') }}?time={{ time() }}"></script>
    <script>
        $(document).ready(function() {
            $('#cartaPastoral').DataTable(
                {
                    language: {
                        emptyTable: "Nenhum registro encontrado",
                        info: "Mostrando _START_ até _END_ de _TOTAL_ registros",
                        infoEmpty: "Mostrando 0 até 0 de 0 registros",
                        infoFiltered: "(filtrado de _MAX_ registros no total)",
                        lengthMenu: "Mostrar _MENU_ registros",
                        loadingRecords: "Carregando...",
                        processing: "Processando...",
                        search: "Pesquisar:",
                        zeroRecords: "Nenhum registro correspondente encontrado",
                        paginate: {
                            first: "Primeiro",
                            last: "Último",
                            next: "Próximo",
                            previous: "Anterior"
                        }
                    }
                }
            );
        });
    </script>
@endsection
