<!DOCTYPE html>
<html lang="{{ config('locales.supported.' . app()->getLocale() . '.html_lang', str_replace('_', '-', app()->getLocale())) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Carteirinhas do Evento') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            color: #1f2a44;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            margin: 18px;
        }

        .header {
            border-bottom: 2px solid #3154d4;
            margin-bottom: 14px;
            padding-bottom: 8px;
        }

        .header h1 {
            color: #25347a;
            font-size: 18px;
            margin: 0 0 4px;
            text-transform: uppercase;
        }

        .header p {
            color: #596275;
            margin: 0;
        }

        .empty {
            border: 1px solid #d8deea;
            color: #596275;
            padding: 18px;
            text-align: center;
        }

        .card {
            border: 1px solid #b8c2d8;
            border-radius: 8px;
            display: inline-block;
            height: 205px;
            margin: 0 8px 12px 0;
            padding: 10px;
            page-break-inside: avoid;
            vertical-align: top;
            width: 48%;
        }

        .card-top {
            border-bottom: 1px solid #d8deea;
            border-collapse: collapse;
            margin-bottom: 8px;
            padding-bottom: 6px;
            width: 100%;
        }

        .logo-cell {
            vertical-align: middle;
            width: 48%;
        }

        .event-title-cell {
            color: #25347a;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.2;
            padding-left: 8px;
            text-align: left;
            text-transform: uppercase;
            vertical-align: middle;
            width: 52%;
        }

        .event-logo {
            display: block;
            max-height: 48px;
            max-width: 118px;
        }

        .participant-table {
            border-collapse: collapse;
            width: 100%;
        }

        .participant-info {
            padding-right: 8px;
            vertical-align: top;
            width: 68%;
        }

        .qr-cell {
            text-align: center;
            vertical-align: top;
            width: 32%;
        }

        .label {
            color: #718096;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: .04em;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .value {
            border-bottom: 1px solid #e0e6f0;
            color: #111827;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.25;
            margin-bottom: 8px;
            min-height: 18px;
            padding-bottom: 3px;
        }

        .church {
            font-size: 10px;
            font-weight: normal;
        }

        .qr-cell img {
            height: 92px;
            width: 92px;
        }

        .qr-caption {
            color: #718096;
            font-size: 8px;
            margin-top: 2px;
            text-transform: uppercase;
        }

        .footer {
            border-top: 1px solid #d8deea;
            color: #718096;
            font-size: 8px;
            margin-top: 8px;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $logoEventoPath = public_path('theme/images/logo-evento.png');
        $logoEventoSrc = file_exists($logoEventoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoEventoPath))
            : null;
    @endphp

    <div class="header">
        <h1>{{ __('Carteirinhas do Evento') }}</h1>
        <p>{{ __('Evento') }}: {{ $evento->titulo }}</p>
        <p>{{ __('Gerado em') }} {{ now()->format('d/m/Y H:i') }} | {{ __('Total de participantes') }}: {{ $inscricoes->count() }}</p>
    </div>

    @forelse ($inscricoes as $inscricao)
        <div class="card">
            <table class="card-top">
                <tr>
                    <td class="logo-cell">
                        @if ($logoEventoSrc)
                            <img class="event-logo" src="{{ $logoEventoSrc }}" alt="{{ __('Logo do Evento') }}">
                        @endif
                    </td>
                    <td class="event-title-cell">{{ $evento->titulo }}</td>
                </tr>
            </table>
            <table class="participant-table">
                <tr>
                    <td class="participant-info">
                        <div class="label">{{ __('Nome do candidato') }}</div>
                        <div class="value">{{ $inscricao->nome ?: '-' }}</div>

                        <div class="label">{{ __('Igreja') }}</div>
                        <div class="value church">{{ $inscricao->igreja_nome ?: '-' }}</div>
                    </td>
                    <td class="qr-cell">
                        <img src="{{ $inscricao->qr_code }}" alt="{{ __('QR Code') }}">
                        <div class="qr-caption">{{ __('QR Code') }}</div>
                    </td>
                </tr>
            </table>
            <div class="footer">{{ __('Apresente esta carteirinha para confirmação de presença.') }}</div>
        </div>
    @empty
        <div class="empty">{{ __('Nenhum participante inscrito neste evento.') }}</div>
    @endforelse
</body>
</html>
