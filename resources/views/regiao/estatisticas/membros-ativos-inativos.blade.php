@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Estatísticas', 'url' => '#', 'active' => false],
        ['text' => 'Membros Ativos e Inativos', 'url' => '#', 'active' => true],
    ]" />
@endsection

@section('extras-css')
<style>
    .membership-comparison {
        --comparison-border: #dce4ee;
        --comparison-start: #edf3fc;
        --comparison-end: #edf7f5;
        min-width: 800px;
        table-layout: fixed;
        border: 1px solid var(--comparison-border);
    }
    .membership-comparison th,
    .membership-comparison td {
        text-align: center;
        vertical-align: middle !important;
        padding: 16px 12px;
    }
    .membership-comparison .comparison-institution {
        width: 24%;
        text-align: left;
    }
    .membership-comparison thead .comparison-start { background: var(--comparison-start); }
    .membership-comparison thead .comparison-end { background: var(--comparison-end); }
    .membership-comparison .comparison-divider { border-left: 3px solid var(--comparison-border); }
    .membership-comparison .comparison-title {
        display: block;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .03em;
        color: #53657e;
    }
    .membership-comparison .comparison-date {
        display: block;
        margin-top: 6px;
        color: #193b72;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: normal;
    }
    .membership-comparison thead tr:last-child th {
        font-size: 12px;
        color: #53657e;
    }
    .membership-comparison .comparison-count { font-size: 18px; font-weight: 600; }
    .membership-comparison .comparison-active { color: #16734c; }
    .membership-comparison .comparison-inactive { color: #627086; }
</style>
@endsection

@section('content')
<div class="col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><h4>Membros Ativos e Inativos — {{ $regiao->nome }}</h4></div>
        <div class="widget-content widget-content-area">
            <form method="GET" action="{{ route('regiao.estatistica.membrosAtivosInativos') }}">
                <div class="row align-items-end">
                    <div class="col-md-3 form-group">
                        <label for="nivel">Agrupar por</label>
                        <select id="nivel" name="nivel" class="form-control">
                            @foreach (['regiao' => 'Região', 'distrito' => 'Distrito', 'igreja' => 'Igreja'] as $valor => $nome)
                                <option value="{{ $valor }}" @selected($nivel === $valor)>{{ $nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="periodo">Período eclesiástico</label>
                        <select id="periodo" name="periodo" class="form-control">
                            @foreach ([1 => 'Anuênio', 2 => 'Biênio', 3 => '3 anos', 4 => '4 anos', 5 => '5 anos', 6 => 'Sexênio'] as $valor => $nome)
                                <option value="{{ $valor }}" @selected($periodo === $valor)>{{ $nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="referencia">Data de referência</label>
                        <input id="referencia" name="referencia" type="date" class="form-control" value="{{ $referencia->toDateString() }}" max="{{ now()->toDateString() }}" required>
                        @error('referencia')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-3 form-group"><button class="btn btn-primary" type="submit">Gerar relatório</button></div>
                </div>
            </form>
            <p class="mb-3">Compare quantos membros estavam ativos e inativos no início e no fim do período.</p>
            <div class="table-responsive">
                <table class="table table-striped membership-comparison">
                    <thead>
                        <tr>
                            <th rowspan="2" scope="col" class="comparison-institution">{{ ['regiao' => 'Região', 'distrito' => 'Distrito', 'igreja' => 'Igreja'][$nivel] }}</th>
                            <th colspan="2" scope="colgroup" class="comparison-start comparison-divider">
                                <span class="comparison-title">Início do período</span>
                                @if(count($linhas))<span class="comparison-date">{{ \Carbon\Carbon::parse($linhas[0]['inicio'])->format('d/m/Y') }}</span>@endif
                            </th>
                            <th colspan="2" scope="colgroup" class="comparison-end comparison-divider">
                                <span class="comparison-title">Fim do período</span>
                                @if(count($linhas))<span class="comparison-date">{{ \Carbon\Carbon::parse($linhas[0]['fim'])->format('d/m/Y') }}</span>@endif
                            </th>
                            <th rowspan="2" scope="col" class="comparison-divider">Crescimento<br>dos ativos (%)</th>
                        </tr>
                        <tr>
                            <th scope="col" class="comparison-start comparison-divider">Ativos</th>
                            <th scope="col" class="comparison-start">Inativos</th>
                            <th scope="col" class="comparison-end comparison-divider">Ativos</th>
                            <th scope="col" class="comparison-end">Inativos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($linhas as $linha)
                            <tr>
                                <th scope="row" class="comparison-institution">{{ $linha['instituicao'] }}</th>
                                <td class="comparison-count comparison-active comparison-divider">{{ number_format($linha['ativos_iniciais'], 0, ',', '.') }}</td>
                                <td class="comparison-count comparison-inactive">{{ number_format($linha['inativos_iniciais'], 0, ',', '.') }}</td>
                                <td class="comparison-count comparison-active comparison-divider">{{ number_format($linha['ativos_finais'], 0, ',', '.') }}</td>
                                <td class="comparison-count comparison-inactive">{{ number_format($linha['inativos_finais'], 0, ',', '.') }}</td>
                                <td class="comparison-divider">
                                    @if ($linha['crescimento_ativos'] === null)
                                        <span class="text-muted">Sem base inicial</span>
                                    @else
                                        <strong class="comparison-count {{ $linha['crescimento_ativos'] > 0 ? 'text-success' : ($linha['crescimento_ativos'] < 0 ? 'text-danger' : 'text-muted') }}">
                                            {{ $linha['crescimento_ativos'] > 0 ? '+' : '' }}{{ number_format($linha['crescimento_ativos'], 2, ',', '.') }}%
                                        </strong>
                                    @endif
                                    <small class="d-block text-muted">{{ number_format($linha['ativos_iniciais'], 0, ',', '.') }} para {{ number_format($linha['ativos_finais'], 0, ',', '.') }} ativos</small>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">Nenhuma instituição encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="text-muted small">Crescimento dos ativos = (ativos finais − ativos iniciais) ÷ ativos iniciais × 100. Quando não há ativos no início e há no fim, o percentual não é calculável.</p>
        </div>
    </div>
</div>
@endsection
