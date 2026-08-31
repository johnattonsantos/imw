@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'Membros por Ministério', 'url' => '#', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@include('extras.alerts')

@php
use Carbon\Carbon;
use App\Support\PeriodoEclesiastico;

$periodoSelecionado = request()->input('periodo_anos', $periodoAnos ?? 1);
$dataFinalSelecionada = request()->input('data_final', $dataFinal ?? Carbon::now()->format('Y-m-d'));
$dataInicialPeriodo = PeriodoEclesiastico::porQuantidadeAnos((int) $periodoSelecionado, Carbon::parse($dataFinalSelecionada))[0]->format('Y-m-d');
$dataInicialCalculada = $dataInicial ?? $dataInicialPeriodo;
@endphp

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Membros por Ministério') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">{{ __('* Distrito:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control" id="distrito" name="distrito" required>
                            <option value="">{{ __('Selecione') }}</option>
                            <option value="all" {{ request()->input('distrito') == 'all' ? 'selected' : '' }}>{{ __('Todos') }}</option>
                            @foreach($distritos as $distrito)
                                <option value="{{ $distrito->id }}" {{ request()->input('distrito') == $distrito->id ? 'selected' : '' }}>{{ $distrito->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <input type="hidden" id="data_inicial" name="data_inicial" value="{{ $dataInicialCalculada }}">

                <div class="form-group row mb-4" id="filtros_data_final">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">{{ __('* Data Final:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="date" class="form-control @error('data_final') is-invalid @enderror" id="data_final" name="data_final" value="{{ $dataFinalSelecionada }}" required>
                    </div>
                </div>
                <div class="form-group row mb-4" id="filtros_periodo">
                    <div class="col-lg-3 text-right">
                        <label class="control-label">{{ __('* Período:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control" id="periodo_anos" name="periodo_anos" required>
                            <option value="1" {{ (string) $periodoSelecionado === '1' ? 'selected' : '' }}>{{ __('Anuênio') }}</option>
                            <option value="2" {{ (string) $periodoSelecionado === '2' ? 'selected' : '' }}>{{ __('Biênio') }}</option>
                            <option value="3" {{ (string) $periodoSelecionado === '3' ? 'selected' : '' }}>{{ __('3 anos') }}</option>
                            <option value="4" {{ (string) $periodoSelecionado === '4' ? 'selected' : '' }}>{{ __('4 anos') }}</option>
                            <option value="5" {{ (string) $periodoSelecionado === '5' ? 'selected' : '' }}>{{ __('5 anos') }}</option>
                            <option value="6" {{ (string) $periodoSelecionado === '6' ? 'selected' : '' }}>{{ __('Sexênio') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ __('Intervalo máximo permitido: 6 anos.') }}</small>
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

            <form id="report_form" action="{{ url('regiao/relatorio/membrosministerio/pdf') }}" method="POST" target="_blank" style="display: none;">
                @csrf
                <input type="hidden" name="distrito" id="report_distrito">
                <input type="hidden" name="data_inicial" id="report_data_inicial">
                <input type="hidden" name="data_final" id="report_data_final">
                <input type="hidden" name="periodo_anos" id="report_periodo_anos">
            </form>
        </div>
    </div>
</div>

	@if(request()->input('data_final') || request()->input('periodo_anos') || request()->input('data_inicial'))
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mt-3">MEMBROS POR MINISTÉRIO - {{ optional($instituicao)->nome ?? $regiao->nome }}</h6>
                            <div class="table-responsive">
                                <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="30px" style="text-align: left" rowspan="2">{{ __('DISTRITO') }}</th>
                                            <th width="500px" style="text-align: left" rowspan="2">{{ __('IGREJA') }}</th>
                                            <th width="60px" style="text-align: center" colspan="2">{{ __('KIDS') }}</th>
                                            <th width="60px" style="text-align: center" colspan="2">{{ __('CONEXÃO') }}</th>
                                            <th width="60px" style="text-align: center" colspan="2">{{ __('FIRE') }}</th>
                                            <th width="60px" style="text-align: center" colspan="2">{{ __('MOVE') }}</th>
                                            <th width="60px" style="text-align: center" colspan="2">{{ __('HOMENS') }}</th>
                                            <th width="60px" style="text-align: center" colspan="2">{{ __('MULHERES') }}</th>
                                            <th width="60px" style="text-align: center" colspan="2">60+</th>
                                            <th width="60px" style="text-align: center" colspan="2">{{ __('TOTAL') }}</th>
                                        </tr>
                                        <tr>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataInicialCalculada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataFinalSelecionada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataInicialCalculada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataFinalSelecionada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataInicialCalculada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataFinalSelecionada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataInicialCalculada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataFinalSelecionada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataInicialCalculada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataFinalSelecionada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataInicialCalculada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataFinalSelecionada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataInicialCalculada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataFinalSelecionada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataInicialCalculada)->format('d/m/Y') }}</th>
                                            <th width="50px" style="text-align: right">{{ Carbon::parse($dataFinalSelecionada)->format('d/m/Y') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $totalKids_X = 0;
                                        $totalKids_Y = 0;
                                        $totalConexao_X = 0;
                                        $totalConexao_Y = 0;
                                        $totalFire_X = 0;
                                        $totalFire_Y = 0;
                                        $totalMove_X = 0;
                                        $totalMove_Y = 0;
                                        $totalHomens_X = 0;
                                        $totalHomens_Y = 0;
                                        $totalMulheres_X = 0;
                                        $totalMulheres_Y = 0;
                                        $totalSessenta_X = 0;
                                        $totalSessenta_Y = 0;
                                        @endphp
                                        @foreach($lancamentos as $lancamento)
                                        @php
                                        $totalKids_X += $lancamento->Kids_X;
                                        $totalKids_Y += $lancamento->Kids_Y;
                                        $totalConexao_X += $lancamento->Conexao_X;
                                        $totalConexao_Y += $lancamento->Conexao_Y;
                                        $totalFire_X += $lancamento->Fire_X;
                                        $totalFire_Y += $lancamento->Fire_Y;
                                        $totalMove_X += $lancamento->Move_X;
                                        $totalMove_Y += $lancamento->Move_Y;
                                        $totalHomens_X += $lancamento->Homens_X;
                                        $totalHomens_Y += $lancamento->Homens_Y;
                                        $totalMulheres_X += $lancamento->Mulheres_X;
                                        $totalMulheres_Y += $lancamento->Mulheres_Y;
                                        $totalSessenta_X += $lancamento->Sessenta_X;
                                        $totalSessenta_Y += $lancamento->Sessenta_Y;
                                        @endphp
                                        <tr>
                                            <td style="text-align: left; min-width: 250px;">{{ $lancamento->distrito }}</td>
                                            <td style="text-align: left; min-width: 250px;">{{ $lancamento->nome }}</td>
                                            <td style="text-align: right">{{ $lancamento->Kids_X }}</td>
                                            <td style="text-align: right">{{ $lancamento->Kids_Y }}</td>
                                            <td style="text-align: right">{{ $lancamento->Conexao_X }}</td>
                                            <td style="text-align: right">{{ $lancamento->Conexao_Y }}</td>
                                            <td style="text-align: right">{{ $lancamento->Fire_X }}</td>
                                            <td style="text-align: right">{{ $lancamento->Fire_Y }}</td>
                                            <td style="text-align: right">{{ $lancamento->Move_X }}</td>
                                            <td style="text-align: right">{{ $lancamento->Move_Y }}</td>
                                            <td style="text-align: right">{{ $lancamento->Homens_X }}</td>
                                            <td style="text-align: right">{{ $lancamento->Homens_Y }}</td>
                                            <td style="text-align: right">{{ $lancamento->Mulheres_X }}</td>
                                            <td style="text-align: right">{{ $lancamento->Mulheres_Y }}</td>
                                            <td style="text-align: right">{{ $lancamento->Sessenta_X }}</td>
                                            <td style="text-align: right">{{ $lancamento->Sessenta_Y }}</td>
                                            <td style="text-align: right">{{ $lancamento->Kids_X + $lancamento->Conexao_X + $lancamento->Fire_X + $lancamento->Move_X + $lancamento->Homens_X + $lancamento->Mulheres_X + $lancamento->Sessenta_X }}</td>
                                            <td style="text-align: right">{{ $lancamento->Kids_Y + $lancamento->Conexao_Y + $lancamento->Fire_Y + $lancamento->Move_Y + $lancamento->Homens_Y + $lancamento->Mulheres_Y + $lancamento->Sessenta_Y }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="thead-dark">
                                            <th colspan="2" style="text-align: left">{{ __('TOTAL') }}</th>
                                            <th style="text-align: right">{{ $totalKids_X }}</th>
                                            <th style="text-align: right">{{ $totalKids_Y }}</th>
                                            <th style="text-align: right">{{ $totalConexao_X }}</th>
                                            <th style="text-align: right">{{ $totalConexao_Y }}</th>
                                            <th style="text-align: right">{{ $totalFire_X }}</th>
                                            <th style="text-align: right">{{ $totalFire_Y }}</th>
                                            <th style="text-align: right">{{ $totalMove_X }}</th>
                                            <th style="text-align: right">{{ $totalMove_Y }}</th>
                                            <th style="text-align: right">{{ $totalHomens_X }}</th>
                                            <th style="text-align: right">{{ $totalHomens_Y }}</th>
                                            <th style="text-align: right">{{ $totalMulheres_X }}</th>
                                            <th style="text-align: right">{{ $totalMulheres_Y }}</th>
                                            <th style="text-align: right">{{ $totalSessenta_X }}</th>
                                            <th style="text-align: right">{{ $totalSessenta_Y }}</th>
                                            <th style="text-align: right">{{ $totalKids_X + $totalConexao_X + $totalFire_X + $totalMove_X + $totalHomens_X + $totalMulheres_X + $totalSessenta_X }}</th>
                                            <th style="text-align: right">{{ $totalKids_Y + $totalConexao_Y + $totalFire_Y + $totalMove_Y + $totalHomens_Y + $totalMulheres_Y + $totalSessenta_Y }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <button class="btn btn-success btn-rounded" onclick="exportReportToExcel();"><i class="fa fa-file-excel" aria-hidden="true"></i> {{ __('Exportar') }}</button>
                </div>
            </div>
            <!-- Fim do Conteúdo -->
        </div>
    </div>
</div>
@endif

@section('extras-scripts')
<script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
<script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
<script src="{{ asset('theme/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker(window.IMW_SELECTPICKER_OPTIONS || {});

        $('#btn_relatorio').on('click', function(event) {
            var distrito = $('#distrito').val();
            var dataInicial = $('#data_inicial').val();
            var dataFinal = $('#data_final').val();
            var periodoAnos = $('#periodo_anos').val();

            if (!dataInicial || !dataFinal || !periodoAnos || !distrito) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos.');
            } else {
                $('#report_distrito').val(distrito);
                $('#report_data_inicial').val(dataInicial);
                $('#report_data_final').val(dataFinal);
                $('#report_periodo_anos').val(periodoAnos);
                $('#report_form').submit();
            }
        });

        $('#filter_form').submit(function(event) {
            var distrito = $('#distrito').val();
            var dataInicial = $('#data_inicial').val();
            var dataFinal = $('#data_final').val();
            var periodoAnos = $('#periodo_anos').val();

            if (!dataInicial || !dataFinal || !periodoAnos || !distrito) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos.');
            }
        });
    });
</script>
@endsection
@endsection
