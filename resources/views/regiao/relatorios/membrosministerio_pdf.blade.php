<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório de Membros por Ministério - IMW PGA') }}</title>
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
            <div class="title">MEMBROS POR MINISTÉRIO - {{ optional($instituicao)->nome ?? $regiao->nome }}</div>
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
                <th width="20px" style="text-align: left" rowspan="2">{{ __('DISTRITO') }}</th>
                <th width="120px" style="text-align: left" rowspan="2">{{ __('IGREJA') }}</th>
                <th width="40px" style="text-align: center" colspan="2">{{ __('KIDS') }}</th>
                <th width="40px" style="text-align: center" colspan="2">{{ __('CONEXÃO') }}</th>
                <th width="40px" style="text-align: center" colspan="2">{{ __('FIRE') }}</th>
                <th width="40px" style="text-align: center" colspan="2">{{ __('MOVE') }}</th>
                <th width="40px" style="text-align: center" colspan="2">{{ __('HOMENS') }}</th>
                <th width="40px" style="text-align: center" colspan="2">{{ __('MULHERES') }}</th>
                <th width="40px" style="text-align: center" colspan="2">60+</th>
                <th width="40px" style="text-align: center" colspan="2">{{ __('TOTAL') }}</th>
            </tr>
            <tr>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataInicial)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataFinal)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataInicial)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataFinal)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataInicial)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataFinal)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataInicial)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataFinal)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataInicial)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataFinal)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataInicial)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataFinal)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataInicial)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataFinal)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataInicial)->format('d/m/Y') }}</th>
                <th width="40px" style="text-align: right">{{ Carbon::parse($dataFinal)->format('d/m/Y') }}</th>
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
                <td style="text-align: left;">{{ $lancamento->distrito }}</td>
                <td style="text-align: left;">{{ $lancamento->nome }}</td>
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

</body>

</html>
