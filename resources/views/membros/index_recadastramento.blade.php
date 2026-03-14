@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Secretaria', 'url' => '/', 'active' => false],
        ['text' => 'Membros', 'url' => '/membros/', 'active' => true],
    ]">
    </x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/table/datatable/datatables.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />

<style>
    .swal2-popup .swal2-styled.swal2-cancel {
        color: white!important;
    },
</style>
@endsection

@include('extras.alerts')

@section('content')
    <div class="container-fluid d-flex justify-content-end">
        <span class="badge badge-info position-relative mt-3 mb-3 ml-2">
            <span>ROL ATUAL: {{ $countAtual }}</span>
        </span>

        <span class="badge badge-danger position-relative mt-3 mb-3 ml-2">
            <span>ROL PERMANENTE: {{ $countPermanente }}</span>
        </span>

        <span class="badge badge-warning position-relative mt-3 mb-3 ml-2">
            <span>ERROS DE CADASTRO: {{ $countHasErrors }}</span>
        </span>
    </div>
    <!-- TABELA -->
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Lista de Membresia Validação</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="row mb-5" id="searchForm">
                    <div class="col-12">
                        <!-- <div class="form-check form-check-inline">
                            <div class="n-chk">
                                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                                    <input type="radio" name="status" value="rol_atual" class="new-control-input" 
                                           {{ request()->input('status') == 'rol_atual' || request()->input('status') == null ? 'checked' : '' }}>
                                    <span class="new-control-indicator"></span>Ativos
                                </label>
                            </div>
                        </div>
                        <div class="form-check form-check-inline">
                            <div class="n-chk">
                                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                                    <input type="radio" name="status" value="inativo" class="new-control-input"
                                           {{ request()->input('status') == 'inativo' ? 'checked' : '' }}>
                                    <span class="new-control-indicator"></span>Inativos
                                </label>
                            </div>
                        </div>
                        <div class="form-check form-check-inline">
                            <div class="n-chk">
                                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                                    <input type="radio" name="status" value="rol_permanente" class="new-control-input"
                                           {{ request()->input('status') == 'rol_permanente' ? 'checked' : '' }}>
                                    <span class="new-control-indicator"></span>Rol Permanente
                                </label>
                            </div>
                        </div>
                        <div class="form-check form-check-inline">
                            <div class="n-chk">
                                <label class="new-control new-checkbox new-checkbox-rounded checkbox-outline-info">
                                    <input type="radio" name="status" value="has_errors" class="new-control-input"
                                           {{ request()->input('status') == 'has_errors' ? 'checked' : '' }}>
                                    <span class="new-control-indicator"></span>Erros de Cadastro
                                </label>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-4">
                        <label for="searchInput">Pesquisar por nome</label>
                        <input type="text" name="search" id="searchInput" class="form-control form-control-sm" placeholder="Pesquisar..."
                               value="{{ request()->input('search') }}">
                    </div>
                    <div class="col-auto" style="margin-left: -19px; margin-top:30px">
                        <button type="submit" id="searchButton" class="btn btn-primary btn-rounded"><x-bx-search /> Pesquisar</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-4" id="datatable" data-url="{{ route('recadastramento-membro.listRecadastramento') }}">
                        <thead>
                            <tr>
                                <th>ROL</th>
                                <th>NOME</th>
                                <th>STATUS</th>
                                <th>RECEPÇÃO</th>
                                <th>EXCLUSÃO</th>
                                <th>CONGREGAÇÃO</th>
                                <th>VALIDADO</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/table/datatable/datatables.js') }}"></script>
    <script src="{{ asset('custom/js/imw_datatables.js')}}?time={{ time() }}"></script>
    <script src="{{ asset('membros/js/indexRecadastramento.js')}}?time={{ time() }}"></script>
@endsection
