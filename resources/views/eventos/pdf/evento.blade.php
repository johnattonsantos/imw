<!DOCTYPE html>
<html lang="{{ config('locales.supported.' . app()->getLocale() . '.html_lang', str_replace('_', '-', app()->getLocale())) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Relatório de Evento') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            color: #222;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            margin: 24px;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #1d4ed8;
            margin-bottom: 18px;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #1f2a5a;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header p {
            color: #666;
            margin-top: 4px;
        }

        .section {
            margin-top: 16px;
        }

        .section-title {
            background: #eef2ff;
            border-left: 4px solid #1d4ed8;
            color: #1f2a5a;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
            padding: 6px 8px;
            text-transform: uppercase;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #d6dbe6;
            padding: 7px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f5f7fb;
            color: #1f2a5a;
            font-weight: bold;
            width: 28%;
        }

        .content-box {
            border: 1px solid #d6dbe6;
            min-height: 36px;
            padding: 8px;
        }

        .content-box p {
            margin-bottom: 6px;
        }

        .muted {
            color: #777;
        }
    </style>
</head>
<body>
    @php
        $agenda = optional($evento->data_inicio)->format('d/m/Y');
        if ($evento->hora_inicio) {
            $agenda .= ' ' . substr((string) $evento->hora_inicio, 0, 5);
        }
        if ($evento->data_fim) {
            $agenda .= ' ' . __('até') . ' ' . optional($evento->data_fim)->format('d/m/Y');
            if ($evento->hora_fim) {
                $agenda .= ' ' . substr((string) $evento->hora_fim, 0, 5);
            }
        }
    @endphp

    <div class="header">
        <h1>{{ __('Relatório de Evento') }}</h1>
        <p>{{ __('Gerado em') }} {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Dados do Evento') }}</div>
        <table>
            <tr>
                <th>{{ __('Evento') }}</th>
                <td>{{ $evento->titulo }}</td>
            </tr>
            <tr>
                <th>{{ __('Propósito') }}</th>
                <td>{{ optional($evento->proposito)->nome ?: '-' }}</td>
            </tr>
            @if (($evento->evento_distrito_nome ?? '-') !== '-')
                <tr>
                    <th>{{ __('Distrito') }}</th>
                    <td>{{ $evento->evento_distrito_nome }}</td>
                </tr>
            @endif
            @if (($evento->evento_igreja_nome ?? '-') !== '-')
                <tr>
                    <th>{{ __('Igreja') }}</th>
                    <td>{{ $evento->evento_igreja_nome }}</td>
                </tr>
            @endif
            <tr>
                <th>{{ __('Sede/Congregação') }}</th>
                <td>{{ $evento->evento_local_nome ?? '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('Agenda') }}</th>
                <td>{{ $agenda ?: '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('Local informado') }}</th>
                <td>{{ $evento->local ?: '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('Status') }}</th>
                <td>{{ $statusOptions[$evento->status] ?? $evento->status }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Descrição / Agenda') }}</div>
        <div class="content-box">
            {!! $evento->descricao ?: '<span class="muted">-</span>' !!}
        </div>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Equipe') }}</div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Nome') }}</th>
                    <th>{{ __('Função') }}</th>
                    <th>{{ __('Contato') }}</th>
                    <th>{{ __('Líder') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($evento->equipe as $membro)
                    <tr>
                        <td>{{ $membro->nome }}</td>
                        <td>{{ optional($membro->eventoFuncao)->nome ?: ($membro->funcao ?: '-') }}</td>
                        <td>{{ $membro->contato ?: '-' }}</td>
                        <td>{{ $membro->lider ? __('Sim') : __('Não') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">{{ __('Nenhum membro de equipe informado.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Observações') }}</div>
        <div class="content-box">
            {!! $evento->observacoes ?: '<span class="muted">-</span>' !!}
        </div>
    </div>
</body>
</html>
