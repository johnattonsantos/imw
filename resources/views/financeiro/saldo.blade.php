@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Movimento de Caixa', 'url' => '/financeiro/movimento-caixa', 'active' => false],
        ['text' => 'Saldo', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection
@section('extras-css')
<style>
    .badge {
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
    }

    .danger {
        background-color: red;
        color: white;
    }

    .gray {
        background-color: gray;
        color: white;
    }

    .green {
        background-color: green;
        color: white;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">

</div>
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                        <h5 class="mt-3">SALDO {{ strtoupper(\Carbon\Carbon::parse($ultimoCaixa)->addMonthsNoOverflow(1)->isoFormat('MMMM [de] YYYY')) }}</h5>

                            <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>{{ __('CAIXA') }}</th>
                                        <th width="300" style="text-align: right">ÚLTIMO SALDO CONSOLIDADO EM {{ \Carbon\Carbon::parse($ultimoCaixa)->isoFormat('MMMM [de] YYYY') }}</th>
                                        <th width="120" style="text-align: right">{{ __('TOTAIS DE ENTRADAS') }}</th>
                                        <th width="120" style="text-align: right">{{ __('TOTAIS DE SAÍDAS') }}</th>
                                        <th width="120" style="text-align: right">{{ __('TRANSF. ENTRADAS') }}</th>
                                        <th width="120" style="text-align: right">{{ __('TRANSF. SAÍDAS') }}</th>
                                        <th width="120" style="text-align: right">{{ __('SALDO ATUAL') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $totalSaldoFinal = 0;
                                    $totalEntradas = 0;
                                    $totalSaidas = 0;
                                    $totalTransferenciasEntrada = 0;
                                    $totalTransferenciasSaida = 0;
                                    $totalSaldoAtual = 0;
                                    @endphp

                                    @foreach ($caixas as $caixa)
                                    <tr>
                                        <td style="text-align: left">{{ $caixa->caixa }}</td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . number_format($caixa->saldo_final, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . number_format($caixa->total_entradas, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . ($caixa->total_saidas > 0 ? '-' : '') . number_format(abs($caixa->total_saidas), 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . number_format($caixa->total_transferencias_entrada, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ 'R$ ' . ($caixa->total_transferencias_saida > 0 ? '-' : '') . number_format(abs($caixa->total_transferencias_saida), 2, ',', '.') }}
                                        </td>
                                        <td class="saldo-atual" style="text-align: right">
                                            {{ 'R$ ' . number_format($caixa->saldo_atual, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @php
                                    $totalSaldoFinal += $caixa->saldo_final;
                                    $totalEntradas += $caixa->total_entradas;
                                    $totalSaidas += $caixa->total_saidas;
                                    $totalTransferenciasEntrada += $caixa->total_transferencias_entrada;
                                    $totalTransferenciasSaida += $caixa->total_transferencias_saida;
                                    $totalSaldoAtual += $caixa->saldo_atual;
                                    @endphp
                                    @endforeach

                                    {{-- Total de cada caixa --}}
                                    <tr>
                                        <td style="text-align: left"><strong>{{ __('Total dos Caixas') }}</strong></td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . number_format($totalSaldoFinal, 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . number_format($totalEntradas, 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . ($totalSaidas > 0 ? '-' : '') . number_format(abs($totalSaidas), 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . number_format($totalTransferenciasEntrada, 2, ',', '.') }}</strong>
                                        </td>
                                        <td style="text-align: right">
                                            <strong>{{ 'R$ ' . ($totalTransferenciasSaida > 0 ? '-' : '') . number_format(abs($totalTransferenciasSaida), 2, ',', '.') }}</strong>
                                        </td>
                                        <td class="saldo-atual" style="text-align: right">
                                            <strong>{{ 'R$ ' . number_format($totalSaldoAtual, 2, ',', '.') }}</strong>
                                        </td>
                                    </tr>
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
            </div>
            <!-- Fim do Conteúdo -->
        </div>
    </div>
</div>
<script src="{{asset('theme/assets/js/planilha/papaparse.min.js')}}"></script>
<script src="{{asset('theme/assets/js/planilha/FileSaver.min.js')}}"></script>
<script src="{{asset('theme/assets/js/planilha/xlsx.full.min.js')}}"></script>
<script src="{{asset('theme/assets/js/planilha/planilha.js')}}"></script>
<script src="{{asset('theme/assets/js/pages/movimentocaixa.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.saldo-atual').each(function() {
            var saldoText = $(this).text().replace('R$ ', '').replace('.', '').replace(',', '.');
            var saldo = parseFloat(saldoText);

            if (saldo < 0) {
                $(this).html('<span class="badge badge-danger">' + $(this).html() + '</span>');
            } else if (saldo === 0) {
                $(this).html('<span class="badge badge-secondary">' + $(this).html() + '</span>');
            } else {
                $(this).html('<span class="badge badge-success">' + $(this).html() + '</span>');
            }
        });
    });
</script>
@endsection