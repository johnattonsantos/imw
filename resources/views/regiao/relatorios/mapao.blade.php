@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'Mapão', 'url' => route('regiao.relatorio.mapao'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<style>
    .mapao-header {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
    }

    .mapao-region {
        color: #6d7893;
        font-size: 13px;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .mapao-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .mapao-filter {
        background: #f8faff;
        border: 1px solid #e4e9f2;
        border-radius: 12px;
        margin-bottom: 18px;
        padding: 16px;
    }

    .mapao-card {
        background: linear-gradient(145deg, #ffffff 0%, #f7f9ff 100%);
        border: 1px solid #e2e8f3;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(31, 45, 61, .07);
        min-height: 138px;
        overflow: hidden;
        padding: 22px;
        position: relative;
    }

    .mapao-card::before {
        background: linear-gradient(180deg, #4361ee, #20a4f3);
        border-radius: 999px;
        content: '';
        height: 52px;
        opacity: .13;
        position: absolute;
        right: -14px;
        top: -16px;
        width: 52px;
    }

    .mapao-card-title {
        color: #7a839a;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.35;
        margin-bottom: 18px;
        min-height: 36px;
        text-transform: uppercase;
    }

    .mapao-card-value {
        color: #3046d3;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -.03em;
        line-height: 1;
    }

    .mapao-note {
        background: #f7f9fc;
        border: 1px solid #e4e9f2;
        border-radius: 12px;
        color: #59657d;
        font-size: 13px;
        line-height: 1.55;
        padding: 14px 16px;
    }
</style>
@endsection

@include('extras.alerts')

@section('content')
@php
    $formatarValor = function ($card) {
        if ($card['tipo'] === 'moeda') {
            return 'R$ ' . number_format((float) $card['valor'], 2, ',', '.');
        }

        if ($card['tipo'] === 'decimal') {
            return number_format((float) $card['valor'], 1, ',', '.');
        }

        return number_format((float) $card['valor'], 0, ',', '.');
    };
@endphp

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-12">
                    <div class="mapao-header px-3 pt-3">
                        <div>
                            <h4 class="mb-1">{{ __('Mapão') }}</h4>
                            <div class="mapao-region">{{ __('Região') }}: {{ $regiao->nome }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget-content widget-content-area">
            <form method="GET" action="{{ route('regiao.relatorio.mapao') }}" class="mapao-filter">
                <div class="row align-items-end">
                    <div class="col-md-3 col-lg-2 mb-2">
                        <label for="data_inicial">{{ __('Data Inicial') }}</label>
                        <input
                            type="date"
                            name="data_inicial"
                            id="data_inicial"
                            class="form-control form-control-sm @error('data_inicial') is-invalid @enderror"
                            value="{{ $periodos['data_inicial']->toDateString() }}"
                        >
                    </div>
                    <div class="col-md-3 col-lg-2 mb-2">
                        <label for="data_final">{{ __('Data Final') }}</label>
                        <input
                            type="date"
                            name="data_final"
                            id="data_final"
                            class="form-control form-control-sm @error('data_final') is-invalid @enderror"
                            value="{{ $periodos['data_final']->toDateString() }}"
                        >
                    </div>
                    <div class="col-md-4 col-lg-3 mb-2">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Filtrar') }}</button>
                        <a href="{{ route('regiao.relatorio.mapao') }}" class="btn btn-light btn-sm">{{ __('Limpar') }}</a>
                    </div>
                </div>

                @error('data_final')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </form>

            <div class="mapao-grid">
                @foreach ($cards as $card)
                    <div class="mapao-card">
                        <div class="mapao-card-title">{{ __($card['titulo']) }}</div>
                        <div class="mapao-card-value">{{ $formatarValor($card) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mapao-note mt-4">
                <strong>{{ __('Critério das médias') }}:</strong>
                {{ __('a média da arrecadação mensal considera entradas financeiras de') }}
                {{ $periodos['data_inicial']->format('d/m/Y') }}
                {{ __('até') }}
                {{ $periodos['data_final']->format('d/m/Y') }}
                {{ __('divididas por') }} {{ $periodos['meses_periodo'] }} {{ __('mês(es) do período.') }}
                {{ __('As médias de recebimentos e exclusões consideram o mesmo intervalo dividido por') }}
                {{ $periodos['trimestres_periodo'] }} {{ __('trimestre(s) do período.') }}
            </div>
        </div>
    </div>
</div>
@endsection
