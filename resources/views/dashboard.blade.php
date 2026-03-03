@extends('template.layout')
@section('extras-css')
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<style>
    .chart-container {
        position: relative;
        height: 400px; /* Ajuste a altura conforme necessário */
    }
    .chart-loading {
        position: absolute;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.8);
        z-index: 5;
        font-size: 13px;
        font-weight: 600;
    }
    .chart-loading.is-active {
        display: flex;
    }
    .chart-error {
        display: none;
        margin-top: 8px;
        color: #b02a37;
        font-size: 12px;
        font-weight: 600;
    }
    .chart-error.is-active {
        display: block;
    }
    .btn-chart-fullscreen {
        min-width: 34px;
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .card:fullscreen,
    .card:-webkit-full-screen {
        width: 100vw;
        height: 100vh;
        margin: 0;
        border-radius: 0;
    }
    .card:fullscreen .card-body,
    .card:-webkit-full-screen .card-body {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .card:fullscreen .chart-container,
    .card:-webkit-full-screen .chart-container {
        flex: 1;
        height: auto;
        min-height: 0;
    }
    .swal2-popup .swal2-styled.swal2-cancel {
        color: white !important;
    }
    .card-title {
        font-size: 1.25rem;
    }
    .card-text {
        font-size: 1rem;
    }
    .card-body ul li {
        font-size: 1rem;
        display: flex;
        align-items: center;
    }
    .card-body ul li i {
        font-size: 1.25rem;
    }
</style>
@endsection

@section('content')
@include('extras.alerts')
<div class="container-fluid h-100">
<div class="container-fluid h-100">
@if($instituicao->tipoInstituicao->sigla == 'I')
    <div class="row flex-fill mt-4">
        <!-- Conteúdo para tipoInstituicao 'I' -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><b>Membros</b></h6>
                    <p class="card-text">Total: {{ $activeMembrosCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><b>Congregados</b></h6>
                    <p class="card-text">Total: {{ $activeCongregadosCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><b>Visitantes</b></h6>
                    <p class="card-text">Total: {{ $activeVisitantesCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row flex-fill mt-4">
         <!-- Gráfico de Membros Ativos vs Inativos -->
        <div class="col-md-6">
            <div class="card h-100" id="card-membros-chart">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">Gráfico de Membros Ativos vs Inativos</h6>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-membros-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                    <div class="chart-container">
                        <canvas id="membrosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Membresia -->
        <div class="col-md-6">
            <div class="card h-100" id="card-visitantes-chart">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">Membresia (<span id="ano-visitantes-text">{{ $anoVisitantes }}</span>)</h6>
                        <div class="d-flex" style="gap: 8px;">
                            <select id="sexo-membresia-select" class="form-control form-control-sm" style="width: 120px;">
                                <option value="" {{ empty($sexoMembresia) ? 'selected' : '' }}>Sexo</option>
                                <option value="M" {{ $sexoMembresia === 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ $sexoMembresia === 'F' ? 'selected' : '' }}>Feminino</option>
                            </select>
                            <select id="status-membresia-select" class="form-control form-control-sm" style="width: 120px;">
                                <option value="A" {{ $statusMembresia === 'A' ? 'selected' : '' }}>Ativo</option>
                                <option value="I" {{ $statusMembresia === 'I' ? 'selected' : '' }}>Inativo</option>
                            </select>
                            <select id="ano-visitantes-select" class="form-control form-control-sm" style="width: 100px;">
                                @foreach($anosDisponiveis as $ano)
                                    <option value="{{ $ano }}" {{ (int) $anoVisitantes === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-visitantes-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="loading-visitantes" class="chart-loading">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            Carregando...
                        </div>
                        <canvas id="visitantesChart"></canvas>
                    </div>
                    <div id="error-visitantes" class="chart-error"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row flex-fill mt-4">
        <div class="col-md-6">
            <div class="card h-100" id="card-rol-es-chart">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">Entradas x Saidas do Rol (<span id="ano-rol-es-text">{{ $anoRol }}</span>)</h6>
                        <div class="d-flex" style="gap: 8px;">
                            <select id="ano-rol-es-select" class="form-control form-control-sm" style="width: 100px;">
                                @foreach($anosDisponiveis as $ano)
                                    <option value="{{ $ano }}" {{ (int) $anoRol === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-rol-es-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="loading-rol-es" class="chart-loading">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            Carregando...
                        </div>
                        <canvas id="rolEntradasSaidasChart"></canvas>
                    </div>
                    <div id="error-rol-es" class="chart-error"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100" id="card-rol-cresc-chart">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">Crescimento Liquido do Rol (<span id="ano-rol-cresc-text">{{ $anoRol }}</span>)</h6>
                        <div class="d-flex" style="gap: 8px;">
                            <select id="ano-rol-cresc-select" class="form-control form-control-sm" style="width: 100px;">
                                @foreach($anosDisponiveis as $ano)
                                    <option value="{{ $ano }}" {{ (int) $anoRol === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-rol-cresc-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="loading-rol-cresc" class="chart-loading">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            Carregando...
                        </div>
                        <canvas id="rolCrescimentoLiquidoChart"></canvas>
                    </div>
                    <div id="error-rol-cresc" class="chart-error"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row flex-fill mt-4">
        <div class="col-md-12">
            <div class="card h-100" id="card-financeiro-chart">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">Financeiro: Entradas x Saidas (<span id="ano-financeiro-text">{{ $anoFinanceiro }}</span>)</h6>
                        <div class="d-flex" style="gap: 8px;">
                            <select id="ano-financeiro-select" class="form-control form-control-sm" style="width: 100px;">
                                @foreach($anosDisponiveis as $ano)
                                    <option value="{{ $ano }}" {{ (int) $anoFinanceiro === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-financeiro-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <div id="loading-financeiro" class="chart-loading">
                            <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                            Carregando...
                        </div>
                        <canvas id="financeiroEntradasSaidasChart"></canvas>
                    </div>
                    <div id="error-financeiro" class="chart-error"></div>
                </div>
            </div>
        </div>
    </div>
@elseif($instituicao->tipoInstituicao->sigla == 'R')
    <div class="row flex-fill mt-4">
        <div class="col-12 text-center">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><b>Bem-vindo(a) à Área Regional!</b></h6>
                    <p class="card-text">
                        Aqui você pode acessar informações e gerenciar atividades da região. Explore recursos, eventos e relatórios regionais.
                    </p>
                </div>
            </div>
        </div>
    </div>
@elseif($instituicao->tipoInstituicao->sigla == 'D')
<div class="row flex-fill mt-4">
    <div class="col-12 text-center">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h6 class="card-title mb-3"><b>Bem-vindo(a) ao {{ session('session_perfil')->instituicao_nome }}!</b></h6>
                <p class="card-text">
                    Você está acessando uma área distrital. Aqui você pode:
                </p>
                <ul class="text-left" style="display: inline-block; list-style: none; padding: 0;">
                    <li class="mb-2">
                        <i class="fas fa-chart-line mr-2 text-primary"></i>
                        Visualizar relatórios distritais detalhados
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-dollar-sign mr-2 text-success"></i>
                        Acessar informações financeiras do distrito
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-chart-bar mr-2 text-info"></i>
                        Verificar estatísticas e análises de crescimento
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-book-open mr-2 text-warning"></i>
                        Acessar recursos e materiais de apoio
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@elseif($instituicao->tipoInstituicao->sigla == 'G')
<div class="row flex-fill mt-4">
    <div class="col-12 text-center">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h6 class="card-title mb-3"><b>Bem-vindo(a) à Área Geral!</b></h6>
                <p class="card-text mb-4">
                    Você está acessando a área geral da instituição. Aqui você pode gerenciar informações globais, acessar relatórios gerais e muito mais.
                </p>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="feature-box d-flex align-items-center">
                            <i class="fas fa-cogs fa-2x text-primary mr-3"></i>
                            <div>
                                <h6 class="feature-title mb-0">Gerenciar Informações</h6>
                                <small class="text-muted">Administre dados essenciais</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="feature-box d-flex align-items-center">
                            <i class="fas fa-chart-pie fa-2x text-success mr-3"></i>
                            <div>
                                <h6 class="feature-title mb-0">Relatórios Gerais</h6>
                                <small class="text-muted">Acesse dados detalhados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="feature-box d-flex align-items-center">
                            <i class="fas fa-users fa-2x text-info mr-3"></i>
                            <div>
                                <h6 class="feature-title mb-0">Gerenciamento de Usuários</h6>
                                <small class="text-muted">Controle de acessos</small>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-3">Explorar Mais</button>
            </div>
        </div>
    </div>
</div>

@elseif($instituicao->tipoInstituicao->sigla == 'O')
    <div class="row flex-fill mt-4">
        <div class="col-12 text-center">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><b>Bem-vindo(a) ao Órgão Geral!</b></h6>
                    <p class="card-text">
                        Você está acessando a área do órgão geral da instituição. Aqui você pode gerenciar atividades e recursos a nível de órgão.
                    </p>
                </div>
            </div>
        </div>
    </div>
@elseif($instituicao->tipoInstituicao->sigla == 'E')
    <div class="row flex-fill mt-4">
        <div class="col-12 text-center">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><b>Bem-vindo(a) à Área de Estatísticas!</b></h6>
                    <p class="card-text">
                        Aqui você pode visualizar e analisar estatísticas detalhadas sobre a instituição. Acesse gráficos, relatórios e mais.
                    </p>
                </div>
            </div>
        </div>
    </div>
@elseif($instituicao->tipoInstituicao->sigla == 'S')
    <div class="row flex-fill mt-4">
        <div class="col-12 text-center">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><b>Bem-vindo(a) à Secretaria!</b></h6>
                    <p class="card-text">
                        Você está acessando a área da secretaria. Aqui você pode gerenciar documentos, comunicados e outras informações importantes.
                    </p>
                </div>
            </div>
        </div>
    </div>
@else 
    <div class="row flex-fill mt-4">
        <div class="col-12 text-center">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><b>Bem-vindo(a)!</b></h6>
                    <p class="card-text">
                        Bem-vindo(a) ao nosso sistema. Navegue pelos menus para acessar as diferentes funcionalidades disponíveis.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
</div>


<!-- Incluir scripts para os gráficos (por exemplo, Chart.js) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labelsMeses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    const chartDataUrl = "{{ route('dashboard.chart-data') }}";

    const visitantesPorMes = @json(array_values($visitantesPorMes));
    const congregadosPorMes = @json(array_values($congregadosPorMes));
    const membrosPorMes = @json(array_values($membrosPorMes));
    const entradasRolPorMes = @json(array_values($entradasRolPorMes));
    const saidasRolPorMes = @json(array_values($saidasRolPorMes));
    const crescimentoLiquidoRolPorMes = @json(array_values($crescimentoLiquidoRolPorMes));
    const entradasFinanceiroPorMes = @json(array_values($entradasFinanceiroPorMes));
    const saidasFinanceiroPorMes = @json(array_values($saidasFinanceiroPorMes));

    const membrosChartCanvas = document.getElementById('membrosChart');
    if (membrosChartCanvas) {
        new Chart(membrosChartCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Inativos', 'Ativos'],
                datasets: [{
                    data: [{{ $totalInativos }}, {{ $totalAtivos }}],
                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'],
                    borderWidth: 1
                }]
            }
        });
    }

    const visitantesChartCanvas = document.getElementById('visitantesChart');
    const rolEntradasSaidasCanvas = document.getElementById('rolEntradasSaidasChart');
    const rolCrescimentoCanvas = document.getElementById('rolCrescimentoLiquidoChart');
    const financeiroEntradasSaidasCanvas = document.getElementById('financeiroEntradasSaidasChart');

    let visitantesChart = null;
    let rolEntradasSaidasChart = null;
    let rolCrescimentoChart = null;
    let financeiroEntradasSaidasChart = null;

    if (visitantesChartCanvas) {
        visitantesChart = new Chart(visitantesChartCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labelsMeses,
                datasets: [
                    {
                        label: 'Visitantes',
                        data: visitantesPorMes,
                        backgroundColor: 'rgba(255, 206, 86, 0.15)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Congregados',
                        data: congregadosPorMes,
                        backgroundColor: 'rgba(40, 167, 69, 0.15)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Membros',
                        data: membrosPorMes,
                        backgroundColor: 'rgba(54, 162, 235, 0.15)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }

    if (rolEntradasSaidasCanvas) {
        rolEntradasSaidasChart = new Chart(rolEntradasSaidasCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsMeses,
                datasets: [
                    {
                        label: 'Entradas',
                        data: entradasRolPorMes,
                        backgroundColor: 'rgba(54, 162, 235, 0.3)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Saidas',
                        data: saidasRolPorMes,
                        backgroundColor: 'rgba(255, 99, 132, 0.3)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }

    if (rolCrescimentoCanvas) {
        rolCrescimentoChart = new Chart(rolCrescimentoCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Crescimento Liquido',
                    data: crescimentoLiquidoRolPorMes,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }

    if (financeiroEntradasSaidasCanvas) {
        financeiroEntradasSaidasChart = new Chart(financeiroEntradasSaidasCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsMeses,
                datasets: [
                    {
                        label: 'Entradas (R$)',
                        data: entradasFinanceiroPorMes,
                        backgroundColor: 'rgba(40, 167, 69, 0.3)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Saidas (R$)',
                        data: saidasFinanceiroPorMes,
                        backgroundColor: 'rgba(220, 53, 69, 0.3)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }

    function setLoading(id, isLoading) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('is-active', isLoading);
    }

    function setChartError(id, message) {
        const el = document.getElementById(id);
        if (!el) return;
        if (!message) {
            el.textContent = '';
            el.classList.remove('is-active');
            return;
        }
        el.textContent = message;
        el.classList.add('is-active');
    }

    async function carregarDadosGrafico(chart, ano) {
        const sexo = chart === 'visitantes'
            ? (document.getElementById('sexo-membresia-select')?.value || '')
            : '';
        const status = chart === 'visitantes'
            ? (document.getElementById('status-membresia-select')?.value || '')
            : '';
        const url = `${chartDataUrl}?chart=${encodeURIComponent(chart)}&ano=${encodeURIComponent(ano)}&sexo=${encodeURIComponent(sexo)}&status=${encodeURIComponent(status)}`;
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) {
            throw new Error('Falha ao buscar dados do grafico.');
        }
        return response.json();
    }

    async function atualizarGrafico(chart, ano) {
        const mapa = {
            visitantes: { loadingId: 'loading-visitantes', anoTextId: 'ano-visitantes-text', errorId: 'error-visitantes', chartInstance: visitantesChart },
            rol_entradas_saidas: { loadingId: 'loading-rol-es', anoTextId: 'ano-rol-es-text', errorId: 'error-rol-es', chartInstance: rolEntradasSaidasChart },
            rol_crescimento: { loadingId: 'loading-rol-cresc', anoTextId: 'ano-rol-cresc-text', errorId: 'error-rol-cresc', chartInstance: rolCrescimentoChart },
            financeiro: { loadingId: 'loading-financeiro', anoTextId: 'ano-financeiro-text', errorId: 'error-financeiro', chartInstance: financeiroEntradasSaidasChart },
        };

        const cfg = mapa[chart];
        if (!cfg || !cfg.chartInstance) return;

        setChartError(cfg.errorId, '');
        setLoading(cfg.loadingId, true);
        try {
            const payload = await carregarDadosGrafico(chart, ano);
            cfg.chartInstance.data.labels = payload.labels;
            payload.datasets.forEach((dataset, idx) => {
                if (cfg.chartInstance.data.datasets[idx]) {
                    cfg.chartInstance.data.datasets[idx].data = dataset.data;
                }
            });
            cfg.chartInstance.update();

            const anoText = document.getElementById(cfg.anoTextId);
            if (anoText) {
                anoText.textContent = payload.ano;
            }
        } catch (error) {
            console.error(error);
            const message = 'Nao foi possivel atualizar este grafico. Tente novamente.';
            setChartError(cfg.errorId, message);
            if (typeof window.swal === 'function') {
                window.swal('Erro', message, 'error');
            }
        } finally {
            setLoading(cfg.loadingId, false);
        }
    }

    const anoVisitantesSelect = document.getElementById('ano-visitantes-select');
    if (anoVisitantesSelect) {
        anoVisitantesSelect.addEventListener('change', function () {
            atualizarGrafico('visitantes', this.value);
        });
    }

    const sexoMembresiaSelect = document.getElementById('sexo-membresia-select');
    if (sexoMembresiaSelect) {
        sexoMembresiaSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-visitantes-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('visitantes', anoAtual);
        });
    }

    const statusMembresiaSelect = document.getElementById('status-membresia-select');
    if (statusMembresiaSelect) {
        statusMembresiaSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-visitantes-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('visitantes', anoAtual);
        });
    }

    const anoRolEsSelect = document.getElementById('ano-rol-es-select');
    if (anoRolEsSelect) {
        anoRolEsSelect.addEventListener('change', function () {
            atualizarGrafico('rol_entradas_saidas', this.value);
        });
    }

    const anoRolCrescSelect = document.getElementById('ano-rol-cresc-select');
    if (anoRolCrescSelect) {
        anoRolCrescSelect.addEventListener('change', function () {
            atualizarGrafico('rol_crescimento', this.value);
        });
    }

    const anoFinanceiroSelect = document.getElementById('ano-financeiro-select');
    if (anoFinanceiroSelect) {
        anoFinanceiroSelect.addEventListener('change', function () {
            atualizarGrafico('financeiro', this.value);
        });
    }

    function abrirFullscreen(targetId) {
        const el = document.getElementById(targetId);
        if (!el) return;

        if (document.fullscreenElement) {
            document.exitFullscreen?.();
            return;
        }

        if (el.requestFullscreen) {
            el.requestFullscreen();
            return;
        }

        if (el.webkitRequestFullscreen) {
            el.webkitRequestFullscreen();
        }
    }

    document.querySelectorAll('.btn-chart-fullscreen').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            abrirFullscreen(targetId);
        });
    });

    function resizeChartsFullscreen() {
        [visitantesChart, rolEntradasSaidasChart, rolCrescimentoChart, financeiroEntradasSaidasChart].forEach(function (chart) {
            if (chart && typeof chart.resize === 'function') {
                chart.resize();
            }
        });
    }

    document.addEventListener('fullscreenchange', resizeChartsFullscreen);
    document.addEventListener('webkitfullscreenchange', resizeChartsFullscreen);

</script>
@endsection
