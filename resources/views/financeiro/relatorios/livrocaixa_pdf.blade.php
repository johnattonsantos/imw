<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório Livro Caixa') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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
            font-size: 16px;
            font-weight: bold;
        }

        .header .info .period {
            font-size: 14px;
        }

        .header .date {
            font-size: 10px;
            color: #555;
            text-align: right;
        }

        h4,
        h5 {
            color: #333;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        th,
        td {
            padding: 6px;
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
            <div class="title">LIVRO CAIXA - {{ session('session_perfil')->instituicao_nome }}</div>
            <div class="period">
                Período de {{ request()->input('dt') }}
            </div>
        </div>
        <div class="date">Data do Relatório: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
    </div>

    <h4>{{ __('Discriminação de saldos por caixa') }}</h4>
    <table>
        <thead>
            <tr>
                <th>{{ __('CAIXA') }}</th>
                <th class="text-right">{{ __('SALDO ANTERIOR') }}</th>
                <th class="text-right">{{ __('TOTAIS DE ENTRADAS') }}</th>
                <th class="text-right">{{ __('TOTAIS DE SAÍDAS') }}</th>
                <th class="text-right">{{ __('TRANSF. ENTRADAS') }}</th>
                <th class="text-right">{{ __('TRANSF. SAÍDAS') }}</th>
                <th class="text-right">{{ __('SALDO ATUAL') }}</th>
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
                <td>{{ $caixa->caixa }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($caixa->saldo_final, 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($caixa->total_entradas, 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . ($caixa->total_saidas > 0 ? '-' : '') . number_format(abs($caixa->total_saidas), 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($caixa->total_transferencias_entrada, 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . ($caixa->total_transferencias_saida > 0 ? '-' : '') . number_format(abs($caixa->total_transferencias_saida), 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($caixa->saldo_atual, 2, ',', '.') }}</td>
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
            <tr class="bold">
                <td>{{ __('Total dos Caixas') }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($totalSaldoFinal, 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($totalEntradas, 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . ($totalSaidas > 0 ? '-' : '') . number_format(abs($totalSaidas), 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($totalTransferenciasEntrada, 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . ($totalTransferenciasSaida > 0 ? '-' : '') . number_format(abs($totalTransferenciasSaida), 2, ',', '.') }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($totalSaldoAtual, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <h5>{{ __('Discriminação dos Lançamentos por Conta') }}</h5>
    <table>
        <thead>
            <tr>
                <th>{{ __('CONTA') }}</th>
                <th>{{ __('CAIXA') }}</th>
                <th class="text-right">{{ __('TOTAL') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $numerosJaExibidos = [];
            $somasPorNumeracao = [];
            @endphp

            {{-- Calcular a soma total para cada numeracao --}}
            @foreach ($lancamentos as $lancamento)
            @php
            if (!isset($somasPorNumeracao[$lancamento->numeracao])) {
            $somasPorNumeracao[$lancamento->numeracao] = 0;
            }
            $somasPorNumeracao[$lancamento->numeracao] += $lancamento->total;
            @endphp
            @endforeach

            {{-- Renderizar a tabela --}}
            @foreach ($lancamentos as $index => $lancamento)
            @if (!in_array($lancamento->numeracao, $numerosJaExibidos))
            <tr class="bold">
                <td>{{ $lancamento->numeracao }}</td>
                <td>{{ $lancamento->nome }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($somasPorNumeracao[$lancamento->numeracao], 2, ',', '.') }}</td>
            </tr>
            @php $numerosJaExibidos[] = $lancamento->numeracao; @endphp
            @endif
            <tr>
                <td></td>
                <td>{{ $lancamento->caixa }}</td>
                <td class="text-right">{{ 'R$ ' . number_format($lancamento->total, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>