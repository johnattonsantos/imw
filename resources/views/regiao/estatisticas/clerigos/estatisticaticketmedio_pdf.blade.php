<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório de Estatística Ticket Médio - IMW PGA') }}</title>
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
            <div class="title">ESTATÍSTICA Ticket Médio - {{ $lancamentos[0]->instituicao }}</div>
        </div>
        <div class="date">Data do Relatório: {{ \Carbon\Carbon::now()->format('m/Y') }}</div>
    </div>

    !
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h6 class="mt-3">QUANTIDADE DE MEMBROS -
                        {{ $lancamentos[0]->escolaridade }}</h6>
                    <div<table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: left;">{{ __('Distrito') }}</th>
                                <th colspan="2" style="text-align: center;">{{ __('Por Igreja') }}</th>
                                <th colspan="2" style="text-align: center;">{{ __('Por Membro') }}</th>
                            </tr>
                            <tr>
                                <th style="text-align: left;"></th>
                                <th style="text-align: center;">{{ __('Valor') }}</th>
                                <th style="text-align: center;">%</th>
                                <th style="text-align: center;">{{ __('Valor') }}</th>
                                <th style="text-align: center;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lancamentos as $lancamento)
                                <tr>
                                    <td>{{ $lancamento->distrito }}</td>

                                    <!-- Itera sobre os itens do distrito -->
                                    @foreach ($lancamento->items as $item)
                                        <td style="text-align: center;">
                                            {{ number_format($item->ticket_medio_igreja, 2) }}
                                            <!-- Ticket médio por igreja -->
                                        </td>
                                        <td style="text-align: center;">
                                            {{ number_format($item->percentual, 2) }}%
                                            <!-- Percentual por igreja -->
                                        </td>
                                        <td style="text-align: center;">
                                            {{ number_format($item->ticket_medio_membro, 2) }}
                                            <!-- Ticket médio por membro -->
                                        </td>
                                        <td style="text-align: center;">
                                            {{ number_format($item->percentual, 2) }}%
                                            <!-- Percentual por membro -->
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="text-align: left;">{{ __('Total Geral') }}</th>
                                <th style="text-align: center;">
                                    {{ number_format($lancamentos->sum('ticket_medio_igreja'), 2) }}
                                </th>
                                <th style="text-align: center;">100%</th>
                                <th style="text-align: center;">
                                    {{ number_format($lancamentos->sum('ticket_medio_membro'), 2) }}
                                </th>
                                <th style="text-align: center;">100%</th>
                            </tr>
                        </tfoot>
                        </table>
                </div>
            </div>
        </div>
    </div>
    </div>



</body>

</html>
