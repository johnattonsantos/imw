<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório Movimento Diário') }}</title>
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
            <div class="title">MOVIMENTO DIÁRIO - {{ session('session_perfil')->instituicao_nome }}</div>
            <div class="period">
                Período de {{ \Carbon\Carbon::parse(request()->input('dt_inicial'))->format('d/m/Y') }} 
                a {{ \Carbon\Carbon::parse(request()->input('dt_final'))->format('d/m/Y') }}
            </div>
        </div>
        <div class="date">Data do Relatório: {{ now()->format('d/m/Y') }}</div>
    </div>

    <h4>{{ __('Discriminação de saldos por caixa') }}</h4>
    <table>
        <thead>
            <tr>
                <th>{{ __('DATA') }}</th>
                <th>{{ __('PLANO DE CONTA') }}</th>
                <th>{{ __('ENTRADA') }}</th>
                <th>{{ __('SAÍDA') }}</th>
                <th>{{ __('ORIGEM/DESTINO') }}</th>
                <th>{{ __('PAGANTE/FAVORECIDO') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Combine all transactions into a single array
                $allLancamentos = [];
                foreach ($lancamentosPorCaixa as $caixaDescricao => $lancamentos) {
                    foreach ($lancamentos as $lancamento) {
                        $lancamento->caixaDescricao = $caixaDescricao; // Add caixaDescricao to each transaction
                        $allLancamentos[] = $lancamento;
                    }
                }

                // Sort all transactions by date
                usort($allLancamentos, function ($a, $b) {
                    return strtotime($a->data_movimento) - strtotime($b->data_movimento);
                });

                // Group transactions by date
                $groupedLancamentos = [];
                foreach ($allLancamentos as $lancamento) {
                    $groupedLancamentos[$lancamento->data_movimento][] = $lancamento;
                }
            @endphp

            @foreach ($groupedLancamentos as $dataMovimento => $lancamentosData)
                <tr>
                    <td><b>{{ \Carbon\Carbon::parse($dataMovimento)->format('d/m/Y') }}</b></td>
                    <td><b>{{ $lancamentosData[0]->caixaDescricao }}</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach ($lancamentosData as $lancamento)
                    <tr>
                        <td></td>
                        <td>{{ $lancamento->planoConta->numeracao }} - {{ $lancamento->planoConta->nome }}</td>
                        <td style="{{ $lancamento->tipo_lancamento === 'E' ? 'color: green;' : 'color: red;' }}">
                            {{ $lancamento->tipo_lancamento === 'E' ? 'R$ ' . number_format($lancamento->valor, 2, ',', '.') : '-' }}
                        </td>
                        <td style="{{ $lancamento->tipo_lancamento === 'S' ? 'color: red;' : 'color: green;' }}">
                            {{ $lancamento->tipo_lancamento === 'S' ? 'R$ ' . number_format($lancamento->valor, 2, ',', '.') : '-' }}
                        </td>
                        <td>{{ $lancamento->descricao }}</td>
                        <td>{{ $lancamento->pagante_favorecido }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
