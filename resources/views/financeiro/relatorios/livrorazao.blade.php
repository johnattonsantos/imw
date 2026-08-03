@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Relatórios - Financeiro', 'url' => '#', 'active' => false],
        ['text' => 'Livro Razão', 'url' => '#', 'active' => true],
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
                        <h4>{{ __('Relatório Livro Razão') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form class="form-vertical" id="filter_form" method="GET">
                    <div class="form-group row mb-4" id="filtros_data">
                        <div class="col-lg-2 text-right">
                            <label class="control-label">{{ __('* Data Inicial:') }}</label>
                        </div>
                        <div class="col-lg-3">
                            <input type="date" class="form-control @error('dt_inicial') is-invalid @enderror" id="dt_inicial" name="dt_inicial" value="{{ request()->input('dt_inicial') }}" required>
                        </div>
                    </div>
                    <div class="for-group row mb-4">
                        <div class="col-lg-2 text-right">
                            <label class="control-label">{{ __('* Data Final:') }}</label>
                        </div>
                        <div class="col-lg-3">
                            <input type="date" class="form-control @error('dt_final') is-invalid @enderror" id="dt_final" name="dt_final" value="{{ request()->input('dt_final') }}" required>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-6">
                            <button id="btn_buscar" type="submit" name="action" value="buscar" title="{{ __('Buscar dados do Relatório') }}" class="btn btn-primary btn">
                                <x-bx-search /> {{ __('Buscar') }}
                            </button>
                            <button id="btn_relatorio" type="button" class="btn btn-secondary">
                                <i class="fa fa-file-pdf"></i> {{ __('Relatório') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (!empty(request()->input('dt_inicial')))
        <div class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4 style="text-transform: uppercase">Livro Caixa - {{ session('session_perfil')->instituicao_nome }}</h4>
                            <p class="pl-3">Período de {{ \Carbon\Carbon::parse(request()->input('dt_inicial'))->format('d/m/Y') }} a {{ \Carbon\Carbon::parse(request()->input('dt_final'))->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="3">{{ __('CONTA/DATA/ORIGEM/DESTINO') }}</th>
                                <th>{{ __('ENTRADA') }} </th>
                                <th class="text-right">{{ __('SAÍDA') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($lancamentosPorConta as $planoConta)
                                <tr style="font-weight: bold; background-color: #f3effc">
                                    <td colspan="5">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                {{ $planoConta->numeracao }} - {{ $planoConta->nome }}
                                            </span>
                                            <span>
                                                Total: {{ $planoConta->total }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                @php
                                    $totalEntradas = 0;
                                    $totalSaidas = 0;
                                @endphp

                                @foreach ($planoConta->lancamentos as $lancamento)

                                    <tr>
                                        <td colspan="3">
                                            {{ \Carbon\Carbon::parse($lancamento->data_lancamento)->format('d/m/Y') }} -
                                            {{ $lancamento->caixa->descricao }} -
                                            {{ $lancamento->pagante_favorecido }}
                                        </td>
                                        <td class="text-right {{ $lancamento->tipo_lancamento === 'E' ? 'green' : '' }}">
                                            {{ $lancamento->tipo_lancamento === 'E' ? 'R$ ' . number_format($lancamento->valor, 2, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-right {{ $lancamento->tipo_lancamento === 'S' ? 'red' : '' }}">
                                            {{ $lancamento->tipo_lancamento === 'S' ? 'R$ ' . number_format($lancamento->valor, 2, ',', '.') : '-' }}
                                        </td>
                                    </tr>
                                    @php
                                        if ($lancamento->tipo_lancamento === 'E') {
                                            $totalEntradas += $lancamento->valor;
                                        } else {
                                            $totalSaidas += abs($lancamento->valor);
                                        }
                                    @endphp
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <button class="btn btn-success btn-rounded" onclick="exportReportToExcel();"><i class="fa fa-file-excel" aria-hidden="true"></i> {{ __('Exportar') }}</button>
                    </div>
                </div>
            </div>
            @endif

    @section('extras-scripts')
        <script>
            $(document).ready(function() {
                $('#btn_relatorio').on('click', function() {
                    var dt_inicial = $('#dt_inicial').val();
                    var dt_final = $('#dt_final').val();

                    if (!dt_inicial || !dt_final) {
                        alert('Por favor, preencha todos os campos obrigatórios.');
                        return;
                    }

                    var url = '{{ url("financeiro/relatorio/livrorazao/pdf") }}' + '?dt_inicial=' + dt_inicial + '&dt_final=' + dt_final;
                    window.open(url, '_blank');
                });

                $('#filter_form').submit(function(event) {
                    var dt_inicial = $('#dt_inicial').val();
                    var dt_final = $('#dt_final').val();

                    if (!dt_inicial || !dt_final) {
                        event.preventDefault();
                        alert('Por favor, preencha todos os campos obrigatórios.');
                    }
                });
            });
        </script>
        <script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
        <script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
        <script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
        <script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
        <script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
    @endsection
@endsection
