<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório de Estatística por Gênero - IMW PGA') }}</title>
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
            <div class="title">ESTATÍSTICA POR GÊNERO - {{ session('session_perfil')->instituicao_nome }}</div>
            <div class="period">
                Período de {{ \Carbon\Carbon::parse(request()->input('data_inicial'))->format('d/m/Y') }}
                a {{ \Carbon\Carbon::parse(request()->input('data_final'))->format('d/m/Y') }}
            </div>
        </div>
        <div class="date">Data do Relatório: {{ \Carbon\Carbon::now()->format('m/Y') }}</div>
    </div>

    <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
    <thead class="thead-dark">
        <tr>
            <th width="200px" style="text-align: left"></th>
            <th colspan="2" style="text-align: center">{{ __('TOTAL MASCULINO') }}</th>
            <th colspan="2" style="text-align: center">{{ __('TOTAL FEMININO') }}</th>
            <th colspan="2" style="text-align: center">{{ __('TOTAL') }}</th>
        </tr>
        <tr>
            <th width="200px" style="text-align: left">{{ __('IGREJA') }}</th>
            <th width="100px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_inicial'))->format('d/m/Y') }}</th>
            <th width="100px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_final'))->format('d/m/Y') }}</th>
            <th width="100px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_inicial'))->format('d/m/Y') }}</th>
            <th width="100px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_final'))->format('d/m/Y') }}</th>
            <th width="100px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_inicial'))->format('d/m/Y') }}</th>
            <th width="100px" style="text-align: center">{{ \Carbon\Carbon::parse(request()->input('data_final'))->format('d/m/Y') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalMasculinoX = 0;
            $totalMasculinoY = 0;
            $totalFemininoX = 0;
            $totalFemininoY = 0;
            $totalX = 0;
            $totalY = 0;
        @endphp
        @foreach($lancamentos as $lancamento)
        <tr>
            <td style="text-align: left; min-width: 100px;">{{ $lancamento->nome }}</td>
            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_masculino_x }}</td>
            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_masculino_y }}</td>
            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_feminino_x }}</td>
            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_feminino_y }}</td>
            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_x }}</td>
            <td style="text-align: center; min-width: 100px;">{{ $lancamento->total_y }}</td>
        </tr>
        @php
            $totalMasculinoX += $lancamento->total_masculino_x;
            $totalMasculinoY += $lancamento->total_masculino_y;
            $totalFemininoX += $lancamento->total_feminino_x;
            $totalFemininoY += $lancamento->total_feminino_y;
            $totalX += $lancamento->total_x;
            $totalY += $lancamento->total_y;
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th style="text-align: center;">{{ __('Total Geral') }}</th>
            <th style="text-align: center;">{{ $totalMasculinoX }}</th>
            <th style="text-align: center;">{{ $totalMasculinoY }}</th>
            <th style="text-align: center;">{{ $totalFemininoX }}</th>
            <th style="text-align: center;">{{ $totalFemininoY }}</th>
            <th style="text-align: center;">{{ $totalX }}</th>
            <th style="text-align: center;">{{ $totalY }}</th>
        </tr>
    </tfoot>
</table>


</body>

</html>