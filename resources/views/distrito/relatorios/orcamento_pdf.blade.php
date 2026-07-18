<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório de Orçamentos - IMW PGA') }}</title>
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

<body>
    <div class="header">
        <img src="{{ public_path('auth/images/login.png') }}" alt="{{ __('Logotipo') }}">
        <div class="info">
            <div class="title">ORÇAMENTO - {{ session('session_perfil')->instituicao_nome }}</div>
            <div class="period" style="margin-top:4px">
                Ano: {{ request()->input('dtano') }}
            </div>
        </div>
        <div class="date">Data do Relatório: {{ \Carbon\Carbon::now()->format('m/Y') }}</div>
    </div>

    <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
    <thead class="thead-dark">
        <tr>
            <th width="180px" style="text-align: left">{{ __('IGREJA') }}</th>
            <th width="50px" style="text-align: right">{{ __('JAN') }}</th>
            <th width="50px" style="text-align: right">{{ __('FEV') }}</th>
            <th width="50px" style="text-align: right">{{ __('MAR') }}</th>
            <th width="50px" style="text-align: right">{{ __('ABR') }}</th>
            <th width="50px" style="text-align: right">{{ __('MAI') }}</th>
            <th width="50px" style="text-align: right">{{ __('JUN') }}</th>
            <th width="50px" style="text-align: right">{{ __('JUL') }}</th>
            <th width="50px" style="text-align: right">{{ __('AGO') }}</th>
            <th width="50px" style="text-align: right">{{ __('SET') }}</th>
            <th width="50px" style="text-align: right">{{ __('OUT') }}</th>
            <th width="50px" style="text-align: right">{{ __('NOV') }}</th>
            <th width="50px" style="text-align: right">{{ __('DEZ') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalJaneiro = 0;
            $totalFevereiro = 0;
            $totalMarco = 0;
            $totalAbril = 0;
            $totalMaio = 0;
            $totalJunho = 0;
            $totalJulho = 0;
            $totalAgosto = 0;
            $totalSetembro = 0;
            $totalOutubro = 0;
            $totalNovembro = 0;
            $totalDezembro = 0;
        @endphp
        @foreach($lancamentos as $lancamento)
            <tr>
                <td>{{ $lancamento->instituicao_nome }}</td>
                <td style="text-align: right">{{ number_format($lancamento->janeiro, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->fevereiro, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->marco, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->abril, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->maio, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->junho, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->julho, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->agosto, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->setembro, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->outubro, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->novembro, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->dezembro, 2, ',', '.') }}</td>
            </tr>
            @php
                $totalJaneiro += $lancamento->janeiro;
                $totalFevereiro += $lancamento->fevereiro;
                $totalMarco += $lancamento->marco;
                $totalAbril += $lancamento->abril;
                $totalMaio += $lancamento->maio;
                $totalJunho += $lancamento->junho;
                $totalJulho += $lancamento->julho;
                $totalAgosto += $lancamento->agosto;
                $totalSetembro += $lancamento->setembro;
                $totalOutubro += $lancamento->outubro;
                $totalNovembro += $lancamento->novembro;
                $totalDezembro += $lancamento->dezembro;
            @endphp
        @endforeach
        <tr class="font-weight-bold">
            <td>{{ __('Totais') }}</td>
            <td style="text-align: right">{{ number_format($totalJaneiro, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalFevereiro, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalMarco, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalAbril, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalMaio, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalJunho, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalJulho, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalAgosto, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalSetembro, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalOutubro, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalNovembro, 2, ',', '.') }}</td>
            <td style="text-align: right">{{ number_format($totalDezembro, 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

</body>

</html>