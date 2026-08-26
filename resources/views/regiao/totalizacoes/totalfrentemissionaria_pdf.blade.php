<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Relatório de Frentes Missionárias') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
        }

        .header img {
            height: 52px;
            display: block;
            margin: 0 auto 8px;
        }

        .title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 11px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            font-size: 11px;
        }

        th,
        td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('auth/images/login.png') }}" alt="{{ __('Logotipo') }}">
        <div class="title">{{ __('Total de Frentes Missionárias') }} - {{ $regiao->nome }}</div>
        <div class="subtitle">
            {{ __('Agrupado por') }}: {{ $tipoInstituicao }} |
            {{ __('Data do Relatório') }}: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-left">{{ __('Instituição') }}</th>
                <th class="text-center">{{ __('Quantidade de Frentes') }}</th>
                <th class="text-center">{{ __('Percentual') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lancamentos as $lancamento)
                <tr>
                    <td class="text-left">{{ $lancamento->nome }}</td>
                    <td class="text-center">{{ $lancamento->total }}</td>
                    <td class="text-center">{{ number_format($lancamento->percentual, 2, ',', '.') }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">{{ __('Nenhum registro encontrado') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="text-left">{{ __('Total Geral') }}</th>
                <th class="text-center">{{ $lancamentos->sum('total') }}</th>
                <th class="text-center">100%</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
