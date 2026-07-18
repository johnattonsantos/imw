<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório Variação Financeira - IMW PGA') }}</title>
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
            <div class="title">VARIAÇÃO FINANCEIRA - {{ $instituicao->nome }}</div>
            <div class="period">
                Período de {{ request()->input('dt_inicial') }}
                a {{ request()->input('dt_final') }}
            </div>
        </div>
        <div class="date">Data do Relatório: {{ \Carbon\Carbon::now()->format('m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="120px" style="text-align: left">{{ __('IGREJA') }}</th>
                <th width="50px" style="text-align: right">{{ __('SALDO ANTERIOR') }}</th>
                <th width="50px" style="text-align: right">{{ __('ENTRADAS') }}</th>
                <th width="50px" style="text-align: right">{{ __('TRANSF. ENTRADAS') }}</th>
                <th width="50px" style="text-align: right">{{ __('SAÍDAS') }}</th>
                <th width="50px" style="text-align: right">{{ __('TRANSF. SAÍDAS') }}</th>
                <th width="50px" style="text-align: right">{{ __('SALDO FINAL') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lancamentos as $lancamento)
            <tr>
                <td>{{ $lancamento->instituicao_nome }}</td>
                <td style="text-align: right">{{ number_format($lancamento->saldo_anterior, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->total_entradas, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->total_transf_entradas, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->total_saidas, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->total_transf_saidas, 2, ',', '.') }}</td>
                <td style="text-align: right">{{ number_format($lancamento->saldo_final, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>