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
            <div class="card card-kpi kpi-soft-blue h-100"><div class="card-body"><small>Total de imóveis</small><h3>{{ number_format((int) $cards['total_imoveis'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi kpi-soft-cyan h-100"><div class="card-body"><small>Total de bens móveis</small><h3>{{ number_format((int) $cards['total_bens_moveis'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi kpi-soft-green h-100"><div class="card-body"><small>Valor total dos imóveis</small><h3>R$ {{ number_format((float) $cards['valor_total_imoveis'], 2, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi kpi-soft-slate h-100"><div class="card-body"><small>Valor total dos bens móveis</small><h3>R$ {{ number_format((float) $cards['valor_total_bens_moveis'], 2, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi kpi-soft-rose h-100"><div class="card-body"><small>Documentos vencidos</small><h3>{{ number_format((int) $cards['documentos_vencidos'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi kpi-soft-amber h-100"><div class="card-body"><small>AVCB vencido</small><h3>{{ number_format((int) $cards['avcb_vencido'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi kpi-soft-indigo h-100"><div class="card-body"><small>Imóveis com regularização pendente</small><h3>{{ number_format((int) $cards['imoveis_regularizacao_pendente'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi kpi-soft-violet h-100"><div class="card-body"><small>Bens depreciados</small><h3>{{ number_format((int) $cards['bens_depreciados'], 0, ',', '.') }}</h3></div></div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-kpi kpi-soft-red h-100"><div class="card-body"><small>Riscos críticos</small><h3>{{ number_format((int) $cards['riscos_criticos'], 0, ',', '.') }}</h3></div></div>
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
    .card-kpi {
        border: 1px solid transparent;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .card-kpi small {
        color: #334155;
        font-weight: 600;
    }
    .card-kpi h3 {
        margin-top: .3rem;
        margin-bottom: 0;
        font-weight: 700;
        color: #0f172a;
    }
    .kpi-soft-blue { background: #eaf2ff; border-color: #d6e5ff; }
    .kpi-soft-cyan { background: #e8f8ff; border-color: #cfefff; }
    .kpi-soft-green { background: #eafaf1; border-color: #d2f2e1; }
    .kpi-soft-slate { background: #f1f5f9; border-color: #e2e8f0; }
    .kpi-soft-rose { background: #ffecef; border-color: #ffd7de; }
    .kpi-soft-amber { background: #fff6e5; border-color: #ffe8be; }
    .kpi-soft-indigo { background: #eef0ff; border-color: #dde2ff; }
    .kpi-soft-violet { background: #f3edff; border-color: #e6dbff; }
    .kpi-soft-red { background: #ffe8e8; border-color: #ffd3d3; }
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
