<!DOCTYPE html>
<html lang="{{ config('locales.supported.' . app()->getLocale() . '.html_lang', str_replace('_', '-', app()->getLocale())) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Crachás do Evento') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            color: #1f2a44;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            margin: 14px;
        }

        .header {
            border-bottom: 2px solid #3154a3;
            margin-bottom: 12px;
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

        .badge-card {
            border: 1px solid #d8deea;
            border-radius: 10px;
            display: inline-block;
            height: 420px;
            margin: 0 8px 14px 0;
            overflow: hidden;
            page-break-inside: avoid;
            position: relative;
            vertical-align: top;
            width: 48%;
        }

        .badge-layout {
            background: #ffffff;
            height: 173px;
            overflow: hidden;
            position: relative;
            width: 100%;
        }

        .badge-layout-img {
            left: 0;
            position: absolute;
            top: 0;
            width: 100%;
        }

        .event-title-strip {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            color: #25347a;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.2;
            min-height: 31px;
            padding: 8px 14px 6px;
            text-align: center;
            text-transform: uppercase;
        }

        .badge-body {
            padding: 8px 18px 0;
            text-align: center;
        }

        .candidate-name {
            color: #111827;
            font-size: 15px;
            font-weight: bold;
            line-height: 1.15;
            margin: 0 0 3px;
            min-height: 32px;
        }

        .candidate-role {
            color: #536179;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-table {
            border-collapse: collapse;
            margin-bottom: 0;
            width: 100%;
        }

        .info-table td {
            border-bottom: 1px solid #e1e7f0;
            padding: 2px 0;
            text-align: left;
            vertical-align: top;
        }

        .details-table {
            border-collapse: collapse;
            margin-top: 6px;
            width: 100%;
        }

        .details-qr {
            padding-top: 2px;
            text-align: left;
            vertical-align: top;
            width: 28%;
        }

        .details-qr img {
            height: 76px;
            width: 76px;
        }

        .details-info {
            padding-left: 10px;
            vertical-align: top;
            width: 72%;
        }

        .info-label {
            color: #8792a8;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: .04em;
            text-transform: uppercase;
            width: 30%;
        }

        .info-value {
            color: #1f2a44;
            font-size: 9px;
            line-height: 1.2;
            width: 70%;
        }

    </style>
</head>
<body>
    @php
        $layoutEventoPath = public_path('theme/images/carteira-digital.png');
        $layoutEventoSrc = file_exists($layoutEventoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($layoutEventoPath))
            : null;
    @endphp

    <div class="header">
        <h1>{{ __('Crachás do Evento') }}</h1>
        <p>{{ __('Evento') }}: {{ $evento->titulo }}</p>
        <p>{{ __('Gerado em') }} {{ now()->format('d/m/Y H:i') }} | {{ __('Total de participantes') }}: {{ $inscricoes->count() }}</p>
    </div>

    @forelse ($inscricoes as $inscricao)
        <div class="badge-card">
            <div class="badge-layout">
                @if ($inscricao->badge_layout || $layoutEventoSrc)
                    <img class="badge-layout-img" src="{{ $inscricao->badge_layout ?: $layoutEventoSrc }}" alt="{{ __('Layout do crachá') }}">
                @endif
            </div>

            <div class="event-title-strip">{{ $evento->titulo }}</div>

            <div class="badge-body">
                <div class="candidate-name">{{ $inscricao->nome ?: '-' }}</div>
                <div class="candidate-role">{{ $inscricao->badge_funcao ?: '-' }}</div>

                <table class="details-table">
                    <tr>
                        <td class="details-qr">
                            <img src="{{ $inscricao->qr_code }}" alt="{{ __('QR Code') }}">
                        </td>
                        <td class="details-info">
                            <table class="info-table">
                                <tr>
                                    <td class="info-label">{{ __('Igreja') }}</td>
                                    <td class="info-value">{{ $inscricao->igreja_nome ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">{{ __('Local') }}</td>
                                    <td class="info-value">{{ $inscricao->badge_local ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-label">{{ __('Estado') }}</td>
                                    <td class="info-value">{{ $inscricao->badge_estado ?: '-' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @empty
        <div class="empty">{{ __('Nenhum participante inscrito neste evento.') }}</div>
    @endforelse
</body>
</html>
