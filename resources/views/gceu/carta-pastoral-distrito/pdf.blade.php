<!DOCTYPE html>
<html>

<head>
    <title>Carta Pastoral - {{ $cartaPastoral->titulo }}</title>
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
            <div class="title">CARTA PASTORAL - {{ $cartaPastoral->nome_igreja }} / {{ session('session_perfil')->instituicao_nome }}</div>
        </div>
        <div class="date">Data do Relatório: {{ now()->format('d/m/Y') }}</div>
    </div>
    <div>
        <div><h4>{{ __('Titulo') }}</h4></div>
        <div style="margin-bottom: 15px;">{{ $cartaPastoral->titulo }}</div>
        <div><h4>{{ __('Carta Pastoral') }}</h4></div>
        <div style="margin-bottom: 15px;">{!! $cartaPastoral->conteudo !!}</div>
        <div><h4>{{ __('Pastor') }}</h4></div>
        <div>{{ $cartaPastoral->pastor }}</div>
    </div>
</body>

</html>
