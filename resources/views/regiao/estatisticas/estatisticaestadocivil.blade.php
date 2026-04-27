@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Estatísticas', 'url' => '#', 'active' => false],
        ['text' => 'Estatística por Estado civil', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
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
                        <h4>Estatísticas por Estado Civil - {{ optional($instituicao)->nome ?? $regiao->nome }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="form-group row mb-4">
                        <div class="col-lg-2 text-right">
                            <label class="control-label">* Distrito:</label>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-control" id="distrito" name="distrito" required>
                                <option value="">Selecione</option>
                                <option value="all" {{ request()->input('distrito') == 'all' ? 'selected' : '' }}>Todos
                                </option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito->id }}"
                                        {{ request()->input('distrito') == $distrito->id ? 'selected' : '' }}>
                                        {{ $distrito->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 text-right">
                            <label class="control-label">* Vínculo:</label>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-control" id="vinculo" name="vinculo" required>
                                <option value="M" {{ ($vinculo ?? request()->input('vinculo', 'M')) == 'M' ? 'selected' : '' }}>
                                    Membro
                                </option>
                                <option value="C" {{ ($vinculo ?? request()->input('vinculo', 'M')) == 'C' ? 'selected' : '' }}>
                                    Congregado
                                </option>
                                <option value="V" {{ ($vinculo ?? request()->input('vinculo', 'M')) == 'V' ? 'selected' : '' }}>
                                    Visitante
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-2">
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

    @if (request()->input('distrito'))
        <div class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area">
                    <!-- Conteúdo -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mt-3">QUANTIDADE DE MEMBROS -
                                        {{ optional($instituicao)->nome ?? $regiao->nome }}</h6>
                                    <div class="table-responsive">
                                        <table id="estado-civil-table" class="table table-striped table-bordered"
                                            style="font-size: 90%; margin-top: 15px;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align: left;">Estado Civil</th>
                                                    <th style="text-align: center;">Total</th>
                                                    <th style="text-align: center;">Percentual</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lancamentos as $lancamento)
                                                    <tr>
                                                        <td>{{ $lancamento->estado_civil }}</td>
                                                        <td style="text-align: center;">{{ $lancamento->total }}</td>
                                                        <td style="text-align: center;">
                                                            {{ number_format($lancamento->percentual, 2) }}%</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th style="text-align: left;">Total Geral</th>
                                                    <th style="text-align: center;">{{ $lancamentos->sum('total') }}</th>
                                                    <th style="text-align: center;">100%</th>
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

@section('extras-scripts')
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>
    <script src="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();

            $('#filter_form').submit(function(event) {
                var distrito = $('#distrito').val();
                var vinculo = $('#vinculo').val();

                if (!distrito || !vinculo) {
                    event.preventDefault();
                    alert('Por favor, preencha todos os campos.');
                }
            });

            if (document.querySelector('#estado-civil-table')) {
                new DataTable('#estado-civil-table', {
                    layout: {
                        topStart: {
                            buttons: [
                                'pageLength',
                                {
                                    extend: 'excel',
                                    className: 'btn btn-primary btn-rounded',
                                    text: '<i class="fas fa-file-excel"></i> Excel',
                                    titleAttr: 'Excel',
                                    title: "ESTATÍSTICA ESTADO CIVIL - {{ optional($instituicao)->nome ?? $regiao->nome }}"
                                },
                                {
                                    extend: 'pdf',
                                    className: 'btn btn-primary btn-rounded',
                                    text: '<i class="fas fa-file-pdf"></i> PDF',
                                    titleAttr: 'PDF',
                                    title: "ESTATÍSTICA ESTADO CIVIL - {{ optional($instituicao)->nome ?? $regiao->nome }}",
                                    customize: function(doc) {
                                        if (doc.content && doc.content[1] && doc.content[1].table) {
                                            doc.content[1].table.widths = ['*', '*', '*'];
                                        }
                                    }
                                }
                            ]
                        }
                    },
                    pageLength: 25,
                    ordering: false,
                    language: {
                        decimal: ",",
                        thousands: ".",
                        emptyTable: "Nenhum registro encontrado.",
                        info: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                        infoEmpty: "Mostrando 0 até 0 de 0 registros",
                        infoFiltered: "(filtrado de _MAX_ registros no total)",
                        infoPostFix: "",
                        lengthMenu: "Mostrar _MENU_ registros",
                        loadingRecords: "Carregando...",
                        processing: "Processando...",
                        search: "Buscar:",
                        zeroRecords: "Nenhum registro encontrado",
                        paginate: {
                            first: "Primeiro",
                            last: "Último",
                            next: "Próximo",
                            previous: "Anterior"
                        },
                        aria: {
                            sortAscending: ": ativar para ordenar a coluna de forma crescente",
                            sortDescending: ": ativar para ordenar a coluna de forma decrescente"
                        }
                    }
                });
            }
        });
    </script>
@endsection
@endsection
