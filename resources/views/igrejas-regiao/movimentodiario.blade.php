@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Igrejas', 'url' => '/igreja', 'active' => false],
        ['text' => 'Movimento Diário', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Relatório Movimento Diário - {{ $instituicao->nome }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4" id="filtros_data">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">{{ __('* Período (Inicial e Final):') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="date" class="form-control @error('dt_inicial') is-invalid @enderror" id="dt_inicial" name="dt_inicial" value="{{ request()->input('dt_inicial') }}" required placeholder="{{ __('ex: 31/12/2000') }}">
                    </div>
                    <div class="col-lg-3">
                        <input type="date" class="form-control @error('dt_final') is-invalid @enderror" id="dt_final" name="dt_final" value="{{ request()->input('dt_final') }}" placeholder="{{ __('ex: 31/12/2000') }}" required>
                    </div>
                </div>

                {{-- Congregação --}}
                <div class="form-group row mb-4">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">{{ __('Caixa:') }}</label>
                    </div>
                    <div class="col-lg-6">
                        <select id="caixa_id" name="caixa_id" class="form-control @error('caixa_id') is-invalid @enderror">
                            <option value="all" {{ request()->input('caixa_id') == '99' ? 'selected' : '' }}>{{ __('Todos') }}
                            </option>
                            @foreach ($caixas as $caixa)
                            <option value="{{ $caixa->id }}" {{ request()->input('caixa_id') == $caixa->id ? 'selected' : '' }}>
                                {{ $caixa->descricao }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-4">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-6">
                        <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn">
                            <x-bx-search /> {{ __('Buscar') }}
                        </button>
                        <button id="btn_relatorio" data-url="{{ route('igreja.movimento-diario-pdf', ['igreja' => $instituicao->id]) }}" type="button" class="btn btn-secondary btn ml-4">
                            <i class="fa fa-file-pdf"></i> {{ __('Relatório') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TABELA -->
@if (!empty(request()->input('dt_inicial')))
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4 style="text-transform: uppercase">Movimento Financeiro -
                            {{ session('session_perfil')->instituicao_nome }}
                    </h4>
                    <p class="pl-3">Período de {{ \Carbon\Carbon::parse(request()->input('dt_inicial'))->format('d/m/Y') }} a {{ \Carbon\Carbon::parse(request()->input('dt_final'))->format('d/m/Y') }}
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-4">
                    <thead>
                        <tr>
                            <th>{{ __('DATA') }}</th>
                            <th>{{ __('PLANO DE CONTA') }}</th>
                            <th>{{ __('ENTRADA') }}</th>
                            <th>{{ __('SAÍDA') }}</th>
                            <th>{{ __('ORIGEM/DESTINO') }}</th>
                            <th>{{ __('PAGANTE/FAVORECIDO') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        // Combine all transactions into a single array
                        $allLancamentos = [];
                        foreach ($lancamentosPorCaixa as $caixaDescricao => $lancamentos) {
                        foreach ($lancamentos as $lancamento) {
                        $lancamento->caixaDescricao = $caixaDescricao; // Add caixaDescricao to each transaction
                        $allLancamentos[] = $lancamento;
                        }
                        }

                        // Sort all transactions by date
                        usort($allLancamentos, function ($a, $b) {
                        return strtotime($a->data_movimento) - strtotime($b->data_movimento);
                        });

                        // Group transactions by date
                        $groupedLancamentos = [];
                        foreach ($allLancamentos as $lancamento) {
                        $groupedLancamentos[$lancamento->data_movimento][] = $lancamento;
                        }
                        @endphp

                        @foreach ($groupedLancamentos as $dataMovimento => $lancamentosData)
                        <tr>
                            <td><b>{{ \Carbon\Carbon::parse($dataMovimento)->format('d/m/Y') }}</b></td>
                            <td><b>{{ $lancamentosData[0]->caixaDescricao }}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($lancamentosData as $lancamento)
                        <tr>
                            <td></td>
                            <td>{{ $lancamento->planoConta->numeracao }} - {{ $lancamento->planoConta->nome }}</td>
                            <td style="{{ $lancamento->tipo_lancamento === 'E' ? 'color: green;' : 'color: red;' }}">
                                {{ $lancamento->tipo_lancamento === 'E' ? 'R$ ' . number_format($lancamento->valor, 2, ',', '.') : '-' }}
                            </td>
                            <td style="{{ $lancamento->tipo_lancamento === 'S' ? 'color: red;' : 'color: green;' }}">
                                {{ $lancamento->tipo_lancamento === 'S' ? 'R$ ' . number_format($lancamento->valor, 2, ',', '.') : '-' }}
                            </td>
                            <td>{{ $lancamento->descricao }}</td>
                            <td>{{ $lancamento->pagante_favorecido }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>


            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 text-center">
            <button class="btn btn-success btn-rounded" onclick="exportReportToExcel();"><i class="fa fa-file-excel" aria-hidden="true"></i> {{ __('Exportar') }}</button>
        </div>
    </div>
</div>
@endif
<script src="{{asset('theme/assets/js/planilha/papaparse.min.js')}}"></script>
<script src="{{asset('theme/assets/js/planilha/FileSaver.min.js')}}"></script>
<script src="{{asset('theme/assets/js/planilha/xlsx.full.min.js')}}"></script>
<script src="{{asset('theme/assets/js/planilha/planilha.js')}}"></script>
<script src="{{asset('theme/assets/js/pages/movimentocaixa.js')}}"></script>
<script>
    document.getElementById('btn_relatorio').addEventListener('click', function () {
        var dt_inicial = document.getElementById('dt_inicial').value;
        var dt_final = document.getElementById('dt_final').value;
        var caixa_id = document.getElementById('caixa_id').value;

        if (!dt_inicial || !dt_final) {
            alert('Por favor, preencha todos os campos obrigatórios: Período (Inicial e Final)');
            return;
        }

        var url = `${$(this).data('url')}?dt_inicial=${dt_inicial}&dt_final=${dt_final}&caixa_id=${caixa_id}`;
        window.open(url, '_blank');
    });
</script>
@endsection
