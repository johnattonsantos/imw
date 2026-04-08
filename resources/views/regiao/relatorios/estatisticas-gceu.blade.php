@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Estatísticas', 'url' => '#', 'active' => false],
    ['text' => 'Estatísticas GCEU', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Relatório Estatísticas GCEU</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table class="table table-bordered table-striped display nowrap" id="estatisticas-gceu-regiao" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Distrito</th>
                            <th>Igreja</th>
                            <th>Quant. GCEUS</th>
                            <th>Quant. Membros</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dados as $item)
                            <tr>
                                <td>{{ $item->distrito }}</td>
                                <td>{{ $item->igreja }}</td>
                                <td>{{ (int) $item->qtd_gceus }}</td>
                                <td>{{ (int) $item->qtd_membros_gceu }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align: right;">TOTAL GERAL:</th>
                            <th>{{ $totais['qtd_gceus'] ?? 0 }}</th>
                            <th>{{ $totais['qtd_membros_gceu'] ?? 0 }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script>
        new DataTable('#estatisticas-gceu-regiao', {
            order: [[2, 'desc']],
            pageLength: 25,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json'
            }
        });
    </script>
@endsection
