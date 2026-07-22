@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Igrejas', 'url' => '/igreja', 'active' => true],
]"></x-breadcrumb>
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
                    <h4>{{ __('Lista de Igrejas') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="row mb-5" id="searchForm">
                <div class="col-4">
                    <input type="text" name="search" id="searchInput" class="form-control form-control-sm" placeholder="{{ __('Pesquisar...') }}">
                </div>
                <div class="col-auto" style="margin-left: -19px;">
                    <button type="submit" id="searchButton" class="btn btn-primary btn-rounded"><x-bx-search /> {{ __('Pesquisar') }}</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-4" id="datatable" data-url="{{ route('igreja.list') }}">
                    <thead>
                        <tr>
                            <th>{{ __('CIDADE') }}</th>
                            <th>{{ __('IGREJA') }}</th>
                            <th>{{ __('PASTOR') }}</th>
                            <th>{{ __('AÇÕES') }}</th>
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
<script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
<script src="{{ asset('theme/plugins/table/datatable/datatables.js') }}"></script>
<script src="{{ asset('custom/js/imw_datatables.js')}}?time={{ time() }}"></script>
<script src="{{ asset('igrejas/js/index.js')}}?time={{ time() }}"></script>
@endsection
