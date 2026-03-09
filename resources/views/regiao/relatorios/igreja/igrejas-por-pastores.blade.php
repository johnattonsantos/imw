@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
        ['text' => 'Igrejas por Pastores', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Relatório Igrejas por Pastores</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="form-group row mb-4">
                        <div class="col-lg-3">
                            <label class="control-label">* Distrito</label>
                            <select class="form-control" id="distrito" name="distrito" required>
                                <option value="all">Todos</option>
                                @foreach($distritos as $distrito)
                                    <option value="{{ $distrito->id }}" {{ request()->input('distrito', 'all') == $distrito->id ? 'selected' : '' }}>
                                        {{ $distrito->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label class="control-label">* Igreja</label>
                            <select class="form-control" id="igreja" name="igreja" required>
                                <option value="all">Todas</option>
                                @foreach($igrejas as $igreja)
                                    <option value="{{ $igreja->id }}" {{ request()->input('igreja', 'all') == $igreja->id ? 'selected' : '' }}>
                                        {{ $igreja->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label class="control-label">* Pastor</label>
                            <select class="form-control" id="pastor" name="pastor" required>
                                <option value="all">Todos</option>
                                @foreach($pastores as $pastor)
                                    <option value="{{ $pastor->id }}" {{ request()->input('pastor', 'all') == $pastor->id ? 'selected' : '' }}>
                                        {{ $pastor->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 d-flex align-items-end">
                            <button id="btn_buscar" type="submit" name="action" value="buscar" class="btn btn-primary">
                                <x-bx-search /> Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (request()->has('action'))
        <div class="col-lg-12 col-12 layout-spacing">
            <h6>IMW - RELATÓRIO IGREJAS POR PASTORES - REGIÃO {{ strtoupper($regiao->nome) }}</h6>
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area">
                    <div class="table-responsive mt-0">
                        <table class="table table-bordered table-striped table-hover mb-4 display nowrap" id="igrejas-por-pastores">
                            <thead>
                                <tr>
                                    <th>DISTRITO</th>
                                    <th>IGREJA</th>
                                    <th>PASTOR</th>
                                    <th>DATA INICIAL</th>
                                    <th>DATA FINAL</th>
                                    <th>MEMBROS NO INÍCIO</th>
                                    <th>MEMBROS NO FINAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lancamentos as $item)
                                    <tr>
                                        <td>{{ $item->distrito_nome }}</td>
                                        <td>{{ $item->igreja_nome }}</td>
                                        <td>{{ $item->pastor_nome }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->data_inicio_nomeacao)->format('d/m/Y') }}</td>
                                        <td>{{ $item->data_fim_nomeacao ? \Carbon\Carbon::parse($item->data_fim_nomeacao)->format('d/m/Y') : 'Atual' }}</td>
                                        <td>{{ $item->total_membros_inicio }}</td>
                                        <td>{{ $item->total_membros_fim }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('extras-scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js"></script>
    <script>
        $('#distrito').change(function () {
            const distritoId = this.value;
            $('#igreja').html('<option value="all">Todas</option>');

            if (distritoId === 'all') {
                return;
            }

            $.get(`/instituicoes/igrejasByDistrito/${distritoId}`, function (igrejas) {
                igrejas.forEach(function (igreja) {
                    $('#igreja').append(`<option value="${igreja.id}">${igreja.nome}</option>`);
                });
            });
        });

        @if(request()->has('action'))
            new DataTable('#igrejas-por-pastores', {
                scrollX: true,
                scrollY: 400,
                scrollCollapse: true,
                layout: {
                    topStart: {
                        buttons: [
                            'pageLength',
                            {
                                extend: 'excel',
                                className: 'btn btn-primary btn-rounded',
                                text: '<i class="fas fa-file-excel"></i> Excel',
                                title: 'IMW - RELATORIO IGREJAS POR PASTORES'
                            },
                            {
                                extend: 'pdf',
                                className: 'btn btn-primary btn-rounded',
                                text: '<i class="fas fa-file-pdf"></i> PDF',
                                title: 'IMW - RELATORIO IGREJAS POR PASTORES',
                                orientation: 'landscape',
                                pageSize: 'A4'
                            }
                        ]
                    },
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json',
                    emptyTable: 'Nenhum resultado encontrado.'
                }
            });
        @endif
    </script>
@endsection
