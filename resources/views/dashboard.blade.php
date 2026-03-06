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
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0"><b>{{ session('session_perfil')->instituicao_nome }} - Indicadores Distritais</b></h6>
            <small class="text-muted">Filtros por gráfico (AJAX)</small>
        </div>
    </div>
</div>

<div class="row flex-fill mt-2">
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-evolucao-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 1 - Evolução de Membros por Igreja (<span id="ano-distrito-evolucao-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-evolucao-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-evolucao-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-evolucao-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-evolucao" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoEvolucaoChart"></canvas>
                </div>
                <div id="error-distrito-evolucao" class="chart-error"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-es-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 2 - Entradas x Saídas de Membros (<span id="ano-distrito-es-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-es-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-es-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-es-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-es" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoEntradasSaidasChart"></canvas>
                </div>
                <div id="error-distrito-es" class="chart-error"></div>
            </div>
        </div>
    </div>
</div>

<div class="row flex-fill mt-4">
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-top-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 3 - Top 10 Igrejas por Total de Membros (<span id="ano-distrito-top-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-top-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-top-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-top-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-top" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoTopIgrejasChart"></canvas>
                </div>
                <div id="error-distrito-top" class="chart-error"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-vinculos-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 4 - Distribuição por Vínculo (<span id="ano-distrito-vinculos-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-vinculos-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-vinculos-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-vinculos-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-vinculos" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoVinculosChart"></canvas>
                </div>
                <div id="error-distrito-vinculos" class="chart-error"></div>
            </div>
        </div>
    </div>
</div>

<div class="row flex-fill mt-4">
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-sexo-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 5 - Novos Membros por Sexo (<span id="ano-distrito-sexo-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-sexo-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-sexo-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-sexo-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-sexo" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoSexoChart"></canvas>
                </div>
                <div id="error-distrito-sexo" class="chart-error"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-status-rol-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 6 - Situação do Rol (<span id="ano-distrito-status-rol-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-status-rol-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-status-rol-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-status-rol-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-status-rol" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoStatusRolChart"></canvas>
                </div>
                <div id="error-distrito-status-rol" class="chart-error"></div>
            </div>
        </div>
    </div>
</div>

<div class="row flex-fill mt-4">
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-crescimento-acumulado-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 7 - Crescimento Líquido Acumulado (<span id="ano-distrito-crescimento-acumulado-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-crescimento-acumulado-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-crescimento-acumulado-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-crescimento-acumulado-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-crescimento-acumulado" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoCrescimentoAcumuladoChart"></canvas>
                </div>
                <div id="error-distrito-crescimento-acumulado" class="chart-error"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-crescimento-igrejas-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 8 - Ranking de Crescimento por Igreja (<span id="ano-distrito-crescimento-igrejas-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-crescimento-igrejas-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-crescimento-igrejas-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-crescimento-igrejas-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-crescimento-igrejas" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoCrescimentoIgrejasChart"></canvas>
                </div>
                <div id="error-distrito-crescimento-igrejas" class="chart-error"></div>
            </div>
        </div>
    </div>
</div>

<div class="row flex-fill mt-4">
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-entradas-igrejas-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 9 - Ranking de Entradas por Igreja (<span id="ano-distrito-entradas-igrejas-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-entradas-igrejas-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-entradas-igrejas-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-entradas-igrejas-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-entradas-igrejas" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoEntradasIgrejasChart"></canvas>
                </div>
                <div id="error-distrito-entradas-igrejas" class="chart-error"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" id="card-distrito-saidas-igrejas-chart">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">Gráfico 10 - Ranking de Saídas por Igreja (<span id="ano-distrito-saidas-igrejas-text">{{ $anoDistrito }}</span>)</h6>
                    <div class="d-flex" style="gap: 8px;">
                        <select id="igreja-distrito-saidas-igrejas-select" class="form-control form-control-sm" style="width: 220px;">
                            <option value="">Todas igrejas</option>
                            @foreach($distritoIgrejas as $igreja)
                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                        <select id="ano-distrito-saidas-igrejas-select" class="form-control form-control-sm" style="width: 100px;">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}" {{ (int) $anoDistrito === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-chart-fullscreen" data-target="card-distrito-saidas-igrejas-chart" title="Tela cheia" aria-label="Tela cheia"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="loading-distrito-saidas-igrejas" class="chart-loading">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        Carregando...
                    </div>
                    <canvas id="distritoSaidasIgrejasChart"></canvas>
                </div>
                <div id="error-distrito-saidas-igrejas" class="chart-error"></div>
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
    const distritoEvolucaoDatasets = @json($distritoEvolucaoDatasets);
    const distritoEntradasPorMes = @json(array_values($distritoEntradasPorMes));
    const distritoSaidasPorMes = @json(array_values($distritoSaidasPorMes));
    const distritoTopIgrejasLabels = @json($distritoTopIgrejasLabels);
    const distritoTopIgrejasTotais = @json($distritoTopIgrejasTotais);
    const distritoVinculosLabels = ['Membros', 'Congregados', 'Visitantes'];
    const distritoVinculosTotais = @json($distritoVinculosTotais);
    const distritoSexoLabels = ['Masculino', 'Feminino', 'Não informado'];
    const distritoSexoTotais = @json($distritoSexoTotais);
    const distritoStatusRolLabels = ['Ativos', 'Inativos'];
    const distritoStatusRolTotais = @json($distritoStatusRolTotais);
    const distritoCrescimentoAcumulado = @json(array_values($distritoCrescimentoAcumulado));
    const distritoCrescimentoIgrejasLabels = @json($distritoCrescimentoIgrejasLabels);
    const distritoCrescimentoIgrejasTotais = @json($distritoCrescimentoIgrejasTotais);
    const distritoEntradasIgrejasLabels = @json($distritoEntradasIgrejasLabels);
    const distritoEntradasIgrejasTotais = @json($distritoEntradasIgrejasTotais);
    const distritoSaidasIgrejasLabels = @json($distritoSaidasIgrejasLabels);
    const distritoSaidasIgrejasTotais = @json($distritoSaidasIgrejasTotais);

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
    const distritoEvolucaoCanvas = document.getElementById('distritoEvolucaoChart');
    const distritoEntradasSaidasCanvas = document.getElementById('distritoEntradasSaidasChart');
    const distritoTopIgrejasCanvas = document.getElementById('distritoTopIgrejasChart');
    const distritoVinculosCanvas = document.getElementById('distritoVinculosChart');
    const distritoSexoCanvas = document.getElementById('distritoSexoChart');
    const distritoStatusRolCanvas = document.getElementById('distritoStatusRolChart');
    const distritoCrescimentoAcumuladoCanvas = document.getElementById('distritoCrescimentoAcumuladoChart');
    const distritoCrescimentoIgrejasCanvas = document.getElementById('distritoCrescimentoIgrejasChart');
    const distritoEntradasIgrejasCanvas = document.getElementById('distritoEntradasIgrejasChart');
    const distritoSaidasIgrejasCanvas = document.getElementById('distritoSaidasIgrejasChart');

    let visitantesChart = null;
    let rolEntradasSaidasChart = null;
    let rolCrescimentoChart = null;
    let financeiroEntradasSaidasChart = null;
    let distritoEvolucaoChart = null;
    let distritoEntradasSaidasChart = null;
    let distritoTopIgrejasChart = null;
    let distritoVinculosChart = null;
    let distritoSexoChart = null;
    let distritoStatusRolChart = null;
    let distritoCrescimentoAcumuladoChart = null;
    let distritoCrescimentoIgrejasChart = null;
    let distritoEntradasIgrejasChart = null;
    let distritoSaidasIgrejasChart = null;

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

    function buildDistritoEvolucaoDatasets(rawDatasets) {
        const palette = [
            'rgba(54, 162, 235, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
        ];

        return rawDatasets.map(function (dataset, index) {
            return {
                label: dataset.label,
                data: dataset.data,
                borderColor: palette[index % palette.length],
                backgroundColor: palette[index % palette.length],
                borderWidth: 2,
                fill: false,
                tension: 0.3
            };
        });
    }

    if (distritoEvolucaoCanvas) {
        distritoEvolucaoChart = new Chart(distritoEvolucaoCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labelsMeses,
                datasets: buildDistritoEvolucaoDatasets(distritoEvolucaoDatasets)
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    if (distritoEntradasSaidasCanvas) {
        distritoEntradasSaidasChart = new Chart(distritoEntradasSaidasCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsMeses,
                datasets: [
                    {
                        label: 'Entradas',
                        data: distritoEntradasPorMes,
                        backgroundColor: 'rgba(40, 167, 69, 0.3)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Saídas',
                        data: distritoSaidasPorMes,
                        backgroundColor: 'rgba(220, 53, 69, 0.3)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    if (distritoTopIgrejasCanvas) {
        distritoTopIgrejasChart = new Chart(distritoTopIgrejasCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: distritoTopIgrejasLabels,
                datasets: [{
                    label: 'Membros',
                    data: distritoTopIgrejasTotais,
                    backgroundColor: 'rgba(23, 162, 184, 0.35)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    if (distritoVinculosCanvas) {
        distritoVinculosChart = new Chart(distritoVinculosCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: distritoVinculosLabels,
                datasets: [{
                    label: 'Cadastros',
                    data: distritoVinculosTotais,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.35)',
                        'rgba(255, 193, 7, 0.35)',
                        'rgba(40, 167, 69, 0.35)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(40, 167, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    }

    if (distritoSexoCanvas) {
        distritoSexoChart = new Chart(distritoSexoCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: distritoSexoLabels,
                datasets: [{
                    label: 'Membros',
                    data: distritoSexoTotais,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.35)',
                        'rgba(255, 99, 132, 0.35)',
                        'rgba(108, 117, 125, 0.35)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    if (distritoStatusRolCanvas) {
        distritoStatusRolChart = new Chart(distritoStatusRolCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: distritoStatusRolLabels,
                datasets: [{
                    label: 'Membros no Rol',
                    data: distritoStatusRolTotais,
                    backgroundColor: ['rgba(40, 167, 69, 0.35)', 'rgba(220, 53, 69, 0.35)'],
                    borderColor: ['rgba(40, 167, 69, 1)', 'rgba(220, 53, 69, 1)'],
                    borderWidth: 1
                }]
            }
        });
    }

    if (distritoCrescimentoAcumuladoCanvas) {
        distritoCrescimentoAcumuladoChart = new Chart(distritoCrescimentoAcumuladoCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Crescimento Líquido Acumulado',
                    data: distritoCrescimentoAcumulado,
                    borderColor: 'rgba(111, 66, 193, 1)',
                    backgroundColor: 'rgba(111, 66, 193, 0.2)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    if (distritoCrescimentoIgrejasCanvas) {
        distritoCrescimentoIgrejasChart = new Chart(distritoCrescimentoIgrejasCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: distritoCrescimentoIgrejasLabels,
                datasets: [{
                    label: 'Saldo',
                    data: distritoCrescimentoIgrejasTotais,
                    backgroundColor: 'rgba(0, 123, 255, 0.35)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    if (distritoEntradasIgrejasCanvas) {
        distritoEntradasIgrejasChart = new Chart(distritoEntradasIgrejasCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: distritoEntradasIgrejasLabels,
                datasets: [{
                    label: 'Entradas',
                    data: distritoEntradasIgrejasTotais,
                    backgroundColor: 'rgba(25, 135, 84, 0.35)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    }

    if (distritoSaidasIgrejasCanvas) {
        distritoSaidasIgrejasChart = new Chart(distritoSaidasIgrejasCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: distritoSaidasIgrejasLabels,
                datasets: [{
                    label: 'Saídas',
                    data: distritoSaidasIgrejasTotais,
                    backgroundColor: 'rgba(253, 126, 20, 0.35)',
                    borderColor: 'rgba(253, 126, 20, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                }
            }
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
        const igrejaPorChart = {
            distrito_evolucao: 'igreja-distrito-evolucao-select',
            distrito_entradas_saidas: 'igreja-distrito-es-select',
            distrito_top_igrejas: 'igreja-distrito-top-select',
            distrito_vinculos: 'igreja-distrito-vinculos-select',
            distrito_sexo_membros: 'igreja-distrito-sexo-select',
            distrito_status_rol: 'igreja-distrito-status-rol-select',
            distrito_crescimento_acumulado: 'igreja-distrito-crescimento-acumulado-select',
            distrito_crescimento_igrejas: 'igreja-distrito-crescimento-igrejas-select',
            distrito_entradas_igrejas: 'igreja-distrito-entradas-igrejas-select',
            distrito_saidas_igrejas: 'igreja-distrito-saidas-igrejas-select',
        };
        const igreja = igrejaPorChart[chart]
            ? (document.getElementById(igrejaPorChart[chart])?.value || '')
            : '';
        const url = `${chartDataUrl}?chart=${encodeURIComponent(chart)}&ano=${encodeURIComponent(ano)}&sexo=${encodeURIComponent(sexo)}&status=${encodeURIComponent(status)}&igreja_id=${encodeURIComponent(igreja)}`;
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
            distrito_evolucao: { loadingId: 'loading-distrito-evolucao', anoTextId: 'ano-distrito-evolucao-text', errorId: 'error-distrito-evolucao', chartInstance: distritoEvolucaoChart },
            distrito_entradas_saidas: { loadingId: 'loading-distrito-es', anoTextId: 'ano-distrito-es-text', errorId: 'error-distrito-es', chartInstance: distritoEntradasSaidasChart },
            distrito_top_igrejas: { loadingId: 'loading-distrito-top', anoTextId: 'ano-distrito-top-text', errorId: 'error-distrito-top', chartInstance: distritoTopIgrejasChart },
            distrito_vinculos: { loadingId: 'loading-distrito-vinculos', anoTextId: 'ano-distrito-vinculos-text', errorId: 'error-distrito-vinculos', chartInstance: distritoVinculosChart },
            distrito_sexo_membros: { loadingId: 'loading-distrito-sexo', anoTextId: 'ano-distrito-sexo-text', errorId: 'error-distrito-sexo', chartInstance: distritoSexoChart },
            distrito_status_rol: { loadingId: 'loading-distrito-status-rol', anoTextId: 'ano-distrito-status-rol-text', errorId: 'error-distrito-status-rol', chartInstance: distritoStatusRolChart },
            distrito_crescimento_acumulado: { loadingId: 'loading-distrito-crescimento-acumulado', anoTextId: 'ano-distrito-crescimento-acumulado-text', errorId: 'error-distrito-crescimento-acumulado', chartInstance: distritoCrescimentoAcumuladoChart },
            distrito_crescimento_igrejas: { loadingId: 'loading-distrito-crescimento-igrejas', anoTextId: 'ano-distrito-crescimento-igrejas-text', errorId: 'error-distrito-crescimento-igrejas', chartInstance: distritoCrescimentoIgrejasChart },
            distrito_entradas_igrejas: { loadingId: 'loading-distrito-entradas-igrejas', anoTextId: 'ano-distrito-entradas-igrejas-text', errorId: 'error-distrito-entradas-igrejas', chartInstance: distritoEntradasIgrejasChart },
            distrito_saidas_igrejas: { loadingId: 'loading-distrito-saidas-igrejas', anoTextId: 'ano-distrito-saidas-igrejas-text', errorId: 'error-distrito-saidas-igrejas', chartInstance: distritoSaidasIgrejasChart },
        };

        const cfg = mapa[chart];
        if (!cfg || !cfg.chartInstance) return;

        setChartError(cfg.errorId, '');
        setLoading(cfg.loadingId, true);
        try {
            const payload = await carregarDadosGrafico(chart, ano);
            cfg.chartInstance.data.labels = payload.labels;
            if (chart === 'distrito_evolucao') {
                cfg.chartInstance.data.datasets = buildDistritoEvolucaoDatasets(payload.datasets || []);
            } else {
                cfg.chartInstance.data.datasets = (payload.datasets || []).map((dataset, idx) => ({
                    ...(cfg.chartInstance.data.datasets[idx] || {}),
                    ...dataset,
                }));
            }
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

    const anoDistritoEvolucaoSelect = document.getElementById('ano-distrito-evolucao-select');
    if (anoDistritoEvolucaoSelect) {
        anoDistritoEvolucaoSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_evolucao', this.value);
        });
    }
    const igrejaDistritoEvolucaoSelect = document.getElementById('igreja-distrito-evolucao-select');
    if (igrejaDistritoEvolucaoSelect) {
        igrejaDistritoEvolucaoSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-evolucao-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_evolucao', anoAtual);
        });
    }

    const anoDistritoEsSelect = document.getElementById('ano-distrito-es-select');
    if (anoDistritoEsSelect) {
        anoDistritoEsSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_entradas_saidas', this.value);
        });
    }
    const igrejaDistritoEsSelect = document.getElementById('igreja-distrito-es-select');
    if (igrejaDistritoEsSelect) {
        igrejaDistritoEsSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-es-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_entradas_saidas', anoAtual);
        });
    }

    const anoDistritoTopSelect = document.getElementById('ano-distrito-top-select');
    if (anoDistritoTopSelect) {
        anoDistritoTopSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_top_igrejas', this.value);
        });
    }
    const igrejaDistritoTopSelect = document.getElementById('igreja-distrito-top-select');
    if (igrejaDistritoTopSelect) {
        igrejaDistritoTopSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-top-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_top_igrejas', anoAtual);
        });
    }

    const anoDistritoVinculosSelect = document.getElementById('ano-distrito-vinculos-select');
    if (anoDistritoVinculosSelect) {
        anoDistritoVinculosSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_vinculos', this.value);
        });
    }
    const igrejaDistritoVinculosSelect = document.getElementById('igreja-distrito-vinculos-select');
    if (igrejaDistritoVinculosSelect) {
        igrejaDistritoVinculosSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-vinculos-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_vinculos', anoAtual);
        });
    }

    const anoDistritoSexoSelect = document.getElementById('ano-distrito-sexo-select');
    if (anoDistritoSexoSelect) {
        anoDistritoSexoSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_sexo_membros', this.value);
        });
    }
    const igrejaDistritoSexoSelect = document.getElementById('igreja-distrito-sexo-select');
    if (igrejaDistritoSexoSelect) {
        igrejaDistritoSexoSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-sexo-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_sexo_membros', anoAtual);
        });
    }

    const anoDistritoStatusRolSelect = document.getElementById('ano-distrito-status-rol-select');
    if (anoDistritoStatusRolSelect) {
        anoDistritoStatusRolSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_status_rol', this.value);
        });
    }
    const igrejaDistritoStatusRolSelect = document.getElementById('igreja-distrito-status-rol-select');
    if (igrejaDistritoStatusRolSelect) {
        igrejaDistritoStatusRolSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-status-rol-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_status_rol', anoAtual);
        });
    }

    const anoDistritoCrescimentoAcumuladoSelect = document.getElementById('ano-distrito-crescimento-acumulado-select');
    if (anoDistritoCrescimentoAcumuladoSelect) {
        anoDistritoCrescimentoAcumuladoSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_crescimento_acumulado', this.value);
        });
    }
    const igrejaDistritoCrescimentoAcumuladoSelect = document.getElementById('igreja-distrito-crescimento-acumulado-select');
    if (igrejaDistritoCrescimentoAcumuladoSelect) {
        igrejaDistritoCrescimentoAcumuladoSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-crescimento-acumulado-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_crescimento_acumulado', anoAtual);
        });
    }

    const anoDistritoCrescimentoIgrejasSelect = document.getElementById('ano-distrito-crescimento-igrejas-select');
    if (anoDistritoCrescimentoIgrejasSelect) {
        anoDistritoCrescimentoIgrejasSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_crescimento_igrejas', this.value);
        });
    }
    const igrejaDistritoCrescimentoIgrejasSelect = document.getElementById('igreja-distrito-crescimento-igrejas-select');
    if (igrejaDistritoCrescimentoIgrejasSelect) {
        igrejaDistritoCrescimentoIgrejasSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-crescimento-igrejas-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_crescimento_igrejas', anoAtual);
        });
    }

    const anoDistritoEntradasIgrejasSelect = document.getElementById('ano-distrito-entradas-igrejas-select');
    if (anoDistritoEntradasIgrejasSelect) {
        anoDistritoEntradasIgrejasSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_entradas_igrejas', this.value);
        });
    }
    const igrejaDistritoEntradasIgrejasSelect = document.getElementById('igreja-distrito-entradas-igrejas-select');
    if (igrejaDistritoEntradasIgrejasSelect) {
        igrejaDistritoEntradasIgrejasSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-entradas-igrejas-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_entradas_igrejas', anoAtual);
        });
    }

    const anoDistritoSaidasIgrejasSelect = document.getElementById('ano-distrito-saidas-igrejas-select');
    if (anoDistritoSaidasIgrejasSelect) {
        anoDistritoSaidasIgrejasSelect.addEventListener('change', function () {
            atualizarGrafico('distrito_saidas_igrejas', this.value);
        });
    }
    const igrejaDistritoSaidasIgrejasSelect = document.getElementById('igreja-distrito-saidas-igrejas-select');
    if (igrejaDistritoSaidasIgrejasSelect) {
        igrejaDistritoSaidasIgrejasSelect.addEventListener('change', function () {
            const anoAtual = document.getElementById('ano-distrito-saidas-igrejas-select')?.value;
            if (!anoAtual) return;
            atualizarGrafico('distrito_saidas_igrejas', anoAtual);
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
        [visitantesChart, rolEntradasSaidasChart, rolCrescimentoChart, financeiroEntradasSaidasChart, distritoEvolucaoChart, distritoEntradasSaidasChart, distritoTopIgrejasChart, distritoVinculosChart, distritoSexoChart, distritoStatusRolChart, distritoCrescimentoAcumuladoChart, distritoCrescimentoIgrejasChart, distritoEntradasIgrejasChart, distritoSaidasIgrejasChart].forEach(function (chart) {
            if (chart && typeof chart.resize === 'function') {
                chart.resize();
            }
        });
    }

    document.addEventListener('fullscreenchange', resizeChartsFullscreen);
    document.addEventListener('webkitfullscreenchange', resizeChartsFullscreen);

</script>
@endsection
