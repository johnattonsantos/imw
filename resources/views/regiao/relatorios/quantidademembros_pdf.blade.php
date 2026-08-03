<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório de Quantidade de Membros - IMW PGA') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        .header .info {
            text-align: left;
            margin-top: 10px;
        }

        .header .info .title {
            font-size: 12px;
            font-weight: bold;
        }

        .header .info .period {
            font-size: 10px;
        }

        .header .date {
            font-size: 8px;
            color: #555;
            text-align: right;
        }

        h4,
        h5 {
            color: #333;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 12px;
        }

        th,
        td {
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

@php
use Carbon\Carbon;
@endphp

<body>
    <div class="header">
        <img src="{{ public_path('auth/images/login.png') }}" alt="{{ __('Logotipo') }}">
        <div class="info">
            <div class="title">QUANTIDADE DE MEMBROS - {{ optional($instituicao)->nome ?? optional($regiao)->nome }}</div>
            <div class="period">
                Período de {{ \Carbon\Carbon::parse($dataInicial)->format('d/m/Y') }}
                a {{ \Carbon\Carbon::parse($dataFinal)->format('d/m/Y') }}
            </div>
        </div>
        <div class="date">Data do Relatório: {{ \Carbon\Carbon::now()->format('m/Y') }}</div>
    </div>

    <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
        <thead class="thead-dark">
            <tr>
                <th style="width: 17%" style="text-align: distrito">{{ __('DISTRITO') }}</th>
                <th style="text-align: left" rowspan="2">{{ __('IGREJA') }}</th>
                <th width="100px" style="text-align: left" rowspan="2">
                    TOTAL EM {{ \Carbon\Carbon::parse($dataInicial)->format('d/m/Y') }}
                </th>
                <th width="100px" style="text-align: left" rowspan="2">
                    TOTAL EM {{ \Carbon\Carbon::parse($dataFinal)->format('d/m/Y') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalGeralInicial = 0;
            $totalGeralFinal = 0;
            @endphp
            @foreach($lancamentos as $lancamento)
            <tr>
                <td style="text-align: left;">{{ $lancamento->distrito }}</td>
                <td style="text-align: left;">{{ $lancamento->nome }}</td>
                <td style="text-align: left;">{{ $lancamento->total_ate_datainicial }}</td>
                <td style="text-align: left;">{{ $lancamento->total_ate_datafinal }}</td>
                @php
                $totalGeralInicial += $lancamento->total_ate_datainicial;
                $totalGeralFinal += $lancamento->total_ate_datafinal;
                @endphp
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align: left;">{{ __('Total Geral') }}</th>
                <th style="text-align: left;">{{ $totalGeralInicial }}</th>
                <th style="text-align: left;">{{ $totalGeralFinal }}</th>
            </tr>
        </tfoot>
    </table>

</body>

</html>
