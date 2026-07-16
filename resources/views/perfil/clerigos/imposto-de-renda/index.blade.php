@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Imposto de Renda', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/table/datatable/datatables.css') }}" rel="stylesheet" type="text/css">
    <style>
        .swal2-popup .swal2-styled.swal2-cancel {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    @include('extras.alerts')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Imposto de Renda') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div>
                    <form action="">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control " name="prebendas_valor_ano" id="prebendas_valor_ano">
                                    @foreach ($prebendas as $prebenda)
                                        <option value="{{ $prebenda->id }}">{{ $prebenda->valorFormatado }}
                                            ({{ $prebenda->ano }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary" id="btn-calcular">{{ __('Calcular') }}</button>
                        </div>

                    </form>
                    <div class="" tabindex="-1" id="body-calculate" aria-hidden="true">
                        <div class="loadable">

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE VISUALIZAÇÃO -->
@endsection

@section('extras-scripts')
    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/table/datatable/datatables.js') }}"></script>
    <script src="{{ asset('custom/js/imw_datatables.js') }}?time={{ time() }}"></script>
    <script src="{{ asset('perfil/clerigos/dependentes/js/index.js') }}?time={{ time() }}"></script>
    <script>
        $('#btn-calcular').click(function(event) {
            event.preventDefault();
            var prebendaId = $('#prebendas_valor_ano').val();

            if (prebendaId) {
                $.ajax({
                    type: 'GET',
                    url: '/usuario/clerigos/perfil/imposto-de-renda/load-html/' + prebendaId,
                    beforeSend: function() {
                        $('#body-calculate').html(`<div style="min-height: 200px; display:flex; justify-content: center; align-items:center;">
                            <div class="spinner-border text-primary align-self-center"></div>
                        </div>`);


                    },
                    success: function(html) {
                        if (html) {
                            $('#body-calculate').html(html);
                        } else {
                            toastr.error('Erro ao carregar dados da prebenda.');
                        }
                    },
                    error: function(error) {
                        $('#body-calculate').html('<div class="text-danger">' + error.responseJSON
                            .message + '</div>');
                        toastr.error('Erro ao visualizar dados desta prebenda.');
                    },
                    complete: function() {
                        $('.loadable').unblock();
                    }
                });
            }
        });
    </script>
@endsection
