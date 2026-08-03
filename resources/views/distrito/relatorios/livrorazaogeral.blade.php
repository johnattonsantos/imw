@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Distritais', 'url' => '#', 'active' => false],
    ['text' => 'Livro Razão Geral', 'url' => '#', 'active' => true],
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
                    <h4>{{ __('Relatório Livro Razão Geral') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form class="form-vertical" id="filter_form" method="GET">
                <div class="form-group row mb-4">
                    <div class="col-lg-2 text-right">
                        <label class="control-label">{{ __('* Data Inicial:') }}</label>
                    </div>
                    <div class="col-lg-3">
                        <input type="date" class="form-control @error('dt_inicial') is-invalid @enderror" id="dt_inicial" name="dt_inicial" value="{{ request()->input('dt_inicial') }}" required>
                    </div>
                </div>

                <div class="form-group row mb-4">
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
                        <button id="btn_relatorio" type="button" name="action" value="relatorio" title="{{ __('Gerar Relatório') }}" class="btn btn-secondary btn">
                            {{ __('Relatório') }}
                        </button>
                    </div>
                </div>
            </form>
            <form id="report_form" action="{{ url('distrito/relatorio/livrorazaogeral/pdf') }}" method="POST" target="_blank" style="display: none;">
                @csrf
                <input type="hidden" name="dt_inicial" id="report_dtinicio">
                <input type="hidden" name="dt_final" id="report_dtfinal">
            </form>
        </div>
    </div>
</div>

@if(request()->input('dt_inicial'))
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mt-3">LIVRO RAZÃO GERAL - {{ session('session_perfil')->instituicao_nome }}</h5>
                            <p>Período de {{ \Carbon\Carbon::parse(request()->input('dt_inicial'))->format('d/m/Y') }} a {{ \Carbon\Carbon::parse(request()->input('dt_final'))->format('d/m/Y') }}</p>
                            <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="200" style="text-align: left">{{ __('CONTAS') }}</th>
                                        <th width="100" style="text-align: right">{{ __('ENTRADAS') }}</th>
                                        <th width="100" style="text-align: right">{{ __('SAÍDAS') }}</th>
                                        <th width="100" style="text-align: right">{{ __('TOTAL') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lancamentos as $key => $group)
                                    <tr style="font-weight: bold;">
                                        <td colspan="3">{{ $key }}</td>
                                        <td style="text-align: right">R$ {{ number_format($group['total'], 2, ',', '.') }}</td>
                                    </tr>
                                    @foreach($group['movimentos'] as $lancamento)
                                    @if($lancamento->total_entradas > 0 || $lancamento->total_saidas > 0)
                                    <tr>
                                        <td>{{ $lancamento->data_movimentacao }} - {{ $lancamento->instituicao_nome }}</td>
                                        <td style="text-align: right">R$ {{ number_format($lancamento->total_entradas, 2, ',', '.') }}</td>
                                        <td style="text-align: right">R$ {{ number_format($lancamento->total_saidas, 2, ',', '.') }}</td>
                                        <td style="text-align: right"></td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endforeach
                                </tbody>
                            </table>

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
<script>
    $(document).ready(function() {

        $('#btn_relatorio').on('click', function(event) {
            var dt_inicial = $('#dt_inicial').val();
            var dt_final = $('#dt_final').val();

            // Verificar se a data está preenchida
            if (!dt_inicial || !dt_final) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos.');
            } else {
                // Preencher e submeter o formulário oculto
                $('#report_dtinicio').val(dt_inicial);
                $('#report_dtfinal').val(dt_final);
                $('#report_form').submit();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#filter_form').submit(function(event) {
            var dataInicial = $('#dt_inicial').val();
            var dataFinal = $('#dt_final').val();

            // Converter as datas para objetos Date
            var dateInicial = new Date(dataInicial);
            var dateFinal = new Date(dataFinal);

            // Verificar se a data final é menor que a data inicial
            if (dateFinal < dateInicial) {
                // Impedir o envio do formulário
                event.preventDefault();
                // Exibir uma mensagem de erro
                alert('O mês final não pode ser menor que o mês inicial.');
            }
        });
    });
</script>
<script>
    function validarFormulario() {
        const dataInicial = document.getElementById('dt_inicial').value;
        const dataFinal = document.getElementById('dt_final').value;

        if (!dataInicial || !dataFinal) {
            alert('Por favor, preencha os campos de mês inicial e mês final.');
            return false;
        }

        return true;
    }

    document.getElementById('btn_buscar').addEventListener('click', function(event) {
        if (!validarFormulario()) {
            event.preventDefault();
        }
    });

    function gerarRelatorio() {
        if (validarFormulario()) {
            const form = document.getElementById('filter_form');
            form.action = "{{ route('financeiro.relatorio-balancete-pdf') }}";
            form.submit();
        }
    }
</script>

<script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
<script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
<script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
@endsection
@endsection