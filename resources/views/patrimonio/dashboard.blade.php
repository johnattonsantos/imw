@extends('template.layout')

@section('content')
@php $podeJuridico = auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.juridico'); @endphp
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-12 mb-3">
            <h4 class="mb-1">Dashboard Patrimonial</h4>
            <p class="text-muted mb-0">Visão consolidada de imóveis, bens móveis, documentação e riscos.</p>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-primary text-white h-100"><div class="card-body"><small>Total de imóveis</small><h3>{{ number_format((int) $cards['total_imoveis'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-info text-white h-100"><div class="card-body"><small>Total de bens móveis</small><h3>{{ number_format((int) $cards['total_bens_moveis'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-success text-white h-100"><div class="card-body"><small>Valor total dos imóveis</small><h3>R$ {{ number_format((float) $cards['valor_total_imoveis'], 2, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-secondary text-white h-100"><div class="card-body"><small>Valor total dos bens móveis</small><h3>R$ {{ number_format((float) $cards['valor_total_bens_moveis'], 2, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-danger text-white h-100"><div class="card-body"><small>Documentos vencidos</small><h3>{{ number_format((int) $cards['documentos_vencidos'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-warning text-dark h-100"><div class="card-body"><small>AVCB vencido</small><h3>{{ number_format((int) $cards['avcb_vencido'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-dark text-white h-100"><div class="card-body"><small>Imóveis com regularização pendente</small><h3>{{ number_format((int) $cards['imoveis_regularizacao_pendente'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-purple text-white h-100"><div class="card-body"><small>Bens depreciados</small><h3>{{ number_format((int) $cards['bens_depreciados'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi bg-danger text-white h-100"><div class="card-body"><small>Riscos críticos</small><h3>{{ number_format((int) $cards['riscos_criticos'], 0, ',', '.') }}</h3></div></div>
        </div>

        <div class="col-lg-4 col-12 mb-4">
            <div class="statbox widget box box-shadow h-100">
                <div class="widget-header p-3"><h5 class="mb-0">Bens móveis por categoria</h5></div>
                <div class="widget-content widget-content-area"><canvas id="chartBensCategoria" height="220"></canvas></div>
            </div>
        </div>

        <div class="col-lg-4 col-12 mb-4">
            <div class="statbox widget box box-shadow h-100">
                <div class="widget-header p-3"><h5 class="mb-0">Imóveis por status de titularidade</h5></div>
                <div class="widget-content widget-content-area"><canvas id="chartImoveisTitularidade" height="220"></canvas></div>
            </div>
        </div>

        <div class="col-lg-4 col-12 mb-4">
            <div class="statbox widget box box-shadow h-100">
                <div class="widget-header p-3"><h5 class="mb-0">Bens por estado de conservação</h5></div>
                <div class="widget-content widget-content-area"><canvas id="chartBensConservacao" height="220"></canvas></div>
            </div>
        </div>

        <div class="col-12 mt-2">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Riscos Jurídicos Altos e Críticos</h4>
                            @if ($podeJuridico)
                                <a href="{{ route('patrimonio.riscos-juridicos.index') }}" class="btn btn-sm btn-dark">Ver todos</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Imóvel</th>
                                    <th>Nível</th>
                                    <th>Status</th>
                                    <th>Data identificação</th>
                                    @if ($podeJuridico)
                                        <th>Ação</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riscosAltosCriticos as $risco)
                                    <tr>
                                        <td>{{ $risco->imovel?->nome ?: ('Imóvel #' . $risco->imovel_id) }}</td>
                                        <td>
                                            @if ($risco->nivel_risco === 'critico')
                                                <span class="badge badge-danger">Crítico</span>
                                            @else
                                                <span class="badge badge-warning">Alto</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $risco->status)) }}</td>
                                        <td>{{ optional($risco->data_identificacao)->format('d/m/Y') ?: '-' }}</td>
                                        @if ($podeJuridico)
                                            <td>
                                                <a href="{{ route('patrimonio.riscos-juridicos.show', $risco->id) }}" class="btn btn-sm btn-info btn-rounded">Ver</a>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $podeJuridico ? 5 : 4 }}" class="text-center">Nenhum risco alto ou crítico no momento.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-css')
<style>
    .card-kpi h3 { margin-top: .3rem; margin-bottom: 0; font-weight: 700; }
    .bg-purple { background: #6f42c1; }
</style>
@endsection

@section('extras-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const charts = @json($charts);

        function buildBarChart(canvasId, labels, values, color) {
            const el = document.getElementById(canvasId);
            if (!el) return;

            new Chart(el.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: color,
                        borderRadius: 6,
                        maxBarThickness: 44
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        buildBarChart('chartBensCategoria', charts.bens_por_categoria.labels, charts.bens_por_categoria.values, '#17a2b8');
        buildBarChart('chartImoveisTitularidade', charts.imoveis_por_titularidade.labels, charts.imoveis_por_titularidade.values, '#28a745');
        buildBarChart('chartBensConservacao', charts.bens_por_conservacao.labels, charts.bens_por_conservacao.values, '#ffc107');
    })();
</script>
@endsection
