@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'IRRF por Igreja', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.css" rel="stylesheet" />
@endsection

@section('content')
@include('extras.alerts')
<div class="col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><h4>IRRF por Igreja - {{ $regiao->nome }}</h4></div>
        <div class="widget-content widget-content-area">
            <form method="GET" action="{{ route('regiao.relatorio.irrf-por-igreja') }}">
                <div class="row align-items-end">
                    <div class="col-md-4 form-group">
                        <label for="data_inicial">Data inicial</label>
                        <input class="form-control" type="date" id="data_inicial" name="data_inicial" value="{{ $inicio }}" required>
                        @error('data_inicial')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="data_final">Data final</label>
                        <input class="form-control" type="date" id="data_final" name="data_final" value="{{ $fim }}" required>
                        @error('data_final')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4 form-group"><button class="btn btn-primary" type="submit">Buscar</button></div>
                </div>
            </form>
            <p>Valores dos lançamentos conciliados, pela data de movimento, nas contas 41 (retenção) e 220 (repasse), incluindo igrejas sem movimento no período.</p>
            <p class="text-muted">“Sem clérigo” indica lançamento sem identificação válida do clérigo, independentemente do nome digitado no favorecido. O destaque é um alerta para conferência, não uma confirmação de irregularidade fiscal. A diferença é retenção menos repasse no período, não um saldo acumulado.</p>
            <div class="table-responsive">
                <table id="irrf-igrejas" class="table table-bordered table-striped" style="width:100%">
                    <thead><tr>
                        <th>Distrito</th><th>Igreja</th><th>Retido (R$)</th><th>Repassado (R$)</th>
                        <th>Diferença (R$)</th><th>Retido sem clérigo (R$)</th><th>Repassado sem clérigo (R$)</th><th>Conferência</th>
                    </tr></thead>
                    <tbody>
                    @foreach($igrejas as $igreja)
                        <tr>
                            <td>{{ $igreja->distrito }}</td><td>{{ $igreja->igreja }}</td>
                            @foreach(['retido', 'repassado', 'diferenca', 'retido_sem_clerigo', 'repassado_sem_clerigo'] as $campo)
                                <td class="text-right" data-order="{{ $igreja->$campo }}">{{ number_format($igreja->$campo, 2, ',', '.') }}</td>
                            @endforeach
                            <td>
                                @if($igreja->quantidade_sem_clerigo > 0)
                                    <span class="text-danger">{{ $igreja->quantidade_sem_clerigo }} lançamento(s) sem clérigo</span>
                                @else
                                    Sem alerta de vínculo
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot><tr>
                        <th>Total da região</th><th></th>
                        @foreach(['retido', 'repassado', 'diferenca', 'retido_sem_clerigo', 'repassado_sem_clerigo'] as $campo)
                            <th class="text-right">{{ number_format($totais[$campo], 2, ',', '.') }}</th>
                        @endforeach
                        <th>{{ $totais['quantidade_sem_clerigo'] }} sem clérigo</th>
                    </tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titulo = @json('IRRF por Igreja - '.$regiao->nome.' - '.\Carbon\Carbon::parse($inicio)->format('d/m/Y').' a '.\Carbon\Carbon::parse($fim)->format('d/m/Y'));
    new DataTable('#irrf-igrejas', {
        scrollX: true,
        order: [],
        layout: {topStart: {buttons: [
            {extend: 'excelHtml5', text: 'Excel', title: titulo, footer: true, exportOptions: {escapeExcelFormula: true}},
            {extend: 'pdfHtml5', text: 'PDF', title: titulo, footer: true, orientation: 'landscape', pageSize: 'A4',
                customize: function (doc) { doc.defaultStyle.fontSize = 8; doc.styles.tableHeader.fontSize = 8; }}
        ]}},
        language: {search: 'Pesquisar:', emptyTable: 'Nenhuma igreja encontrada na região.',
            info: 'Exibindo _START_ a _END_ de _TOTAL_ igrejas', lengthMenu: 'Exibir _MENU_ igrejas',
            zeroRecords: 'Nenhuma igreja corresponde à pesquisa.', infoEmpty: 'Nenhuma igreja para exibir'}
    });
});
</script>
@endsection
