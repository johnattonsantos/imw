<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório de Livro Razão Geral - IMW PGA') }}</title>
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
            <div class="title">LIVRO RAZÃO GERAL - {{ session('session_perfil')->instituicao_nome }}</div>
            <div class="period">
                Período de {{ \Carbon\Carbon::parse(request()->input('dt_inicial'))->format('d/m/Y') }}
                a {{ \Carbon\Carbon::parse(request()->input('dt_final'))->format('d/m/Y') }}
            </div>
        </div>
        <div class="date">Data do Relatório: {{ \Carbon\Carbon::now()->format('m/Y') }}</div>
    </div>

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

</body>

</html>