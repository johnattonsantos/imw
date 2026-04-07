<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Módulo Geral</title>
    <style>
        @page { margin: 62px 16px 40px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; margin: 0; }
        .header {
            position: fixed;
            top: -52px;
            left: 0;
            right: 0;
            height: 46px;
            border-bottom: 1px solid #cfcfcf;
            padding-bottom: 4px;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 24px;
            border-top: 1px solid #cfcfcf;
            color: #666;
            font-size: 8px;
            line-height: 18px;
        }
        h2 { margin: 0 0 2px 0; font-size: 14px; }
        h3 { margin: 8px 0 4px 0; font-size: 10px; }
        .meta { margin: 0; font-size: 8px; color: #555; }
        .kpi { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .kpi td { border: 1px solid #d7d7d7; padding: 4px; }
        .kpi td.label { width: 28%; background: #f5f5f5; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th, td { border: 1px solid #d7d7d7; padding: 3px 4px; }
        th { background: #efefef; text-align: left; }
        .small { font-size: 8px; }
        .avoid-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Módulo Geral Multi-Regional</h2>
        <p class="meta">
            <strong>Período:</strong> {{ $periodoResumo }}
            |
            <strong>Gerado em:</strong> {{ optional($geradoEm)->format('d/m/Y H:i:s') }}
        </p>
    </div>

    <div class="footer">
        IMW | Módulo Geral
    </div>

    <table class="kpi avoid-break">
        <tr>
            <td class="label">Total de Usuários</td><td>{{ $totalUsuarios }}</td>
            <td class="label">Total de Instituições</td><td>{{ $totalInstituicoes }}</td>
        </tr>
        <tr>
            <td class="label">Total de Clérigos</td><td>{{ $totalClerigos }}</td>
            <td class="label">Nomeações Ativas</td><td>{{ $totalNomeacoesAtivas }}</td>
        </tr>
        <tr>
            <td class="label">Usuários Admin Sistema</td><td>{{ $totalUsuariosAdminSistema }}</td>
            <td class="label">Usuários CRIE</td><td>{{ $totalUsuariosCrie }}</td>
        </tr>
        <tr>
            <td class="label">Usuários Sem Região</td><td>{{ $totalUsuariosSemRegiao }}</td>
            <td class="label">Auditorias no Período</td><td>{{ $totalAuditoriasPeriodo }}</td>
        </tr>
        <tr>
            <td class="label">Auditorias Hoje</td><td>{{ $totalAuditoriasHoje }}</td>
            <td class="label">Login Falho (Período)</td><td>{{ $totalAuditoriasLoginFalho }}</td>
        </tr>
    </table>

    <div class="avoid-break">
    <h3>Perfis Estratégicos por Região</h3>
    <table>
        <thead>
            <tr>
                <th>Região</th>
                <th>Admin Sistema</th>
                <th>CRIE</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($perfisEstrategicosPorRegiao as $item)
                <tr>
                    <td>{{ $item->regiao_nome }}</td>
                    <td>{{ $item->total_admin_sistema }}</td>
                    <td>{{ $item->total_crie }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Sem dados</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="avoid-break">
    <h3>Usuários por Região</h3>
    <table>
        <thead><tr><th>Região</th><th>Total</th></tr></thead>
        <tbody>
            @forelse ($usuariosPorRegiao as $item)
                <tr><td>{{ $item->regiao_nome }}</td><td>{{ $item->total }}</td></tr>
            @empty
                <tr><td colspan="2">Sem dados</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="avoid-break">
    <h3>Instituições Ativas por Região</h3>
    <table>
        <thead><tr><th>Região</th><th>Total</th></tr></thead>
        <tbody>
            @forelse ($instituicoesPorRegiao as $item)
                <tr><td>{{ $item->regiao_nome }}</td><td>{{ $item->total }}</td></tr>
            @empty
                <tr><td colspan="2">Sem dados</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="avoid-break">
    <h3>Clérigos por Região</h3>
    <table>
        <thead><tr><th>Região</th><th>Total</th></tr></thead>
        <tbody>
            @forelse ($clerigosPorRegiao as $item)
                <tr><td>{{ $item->regiao_nome }}</td><td>{{ $item->total }}</td></tr>
            @empty
                <tr><td colspan="2">Sem dados</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="avoid-break">
    <h3>Nomeações Ativas por Região</h3>
    <table>
        <thead><tr><th>Região</th><th>Total</th></tr></thead>
        <tbody>
            @forelse ($nomeacoesAtivasPorRegiao as $item)
                <tr><td>{{ $item->regiao_nome }}</td><td>{{ $item->total }}</td></tr>
            @empty
                <tr><td colspan="2">Sem dados</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="avoid-break">
    <h3>Top 25 Nomeações Ativas por Instituição</h3>
    <table>
        <thead><tr><th>Instituição</th><th>Total</th></tr></thead>
        <tbody>
            @forelse ($nomeacoesPorInstituicao as $item)
                <tr><td>{{ $item->instituicao_nome }}</td><td>{{ $item->total }}</td></tr>
            @empty
                <tr><td colspan="2">Sem dados</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="avoid-break">
    <h3>Auditorias por Evento</h3>
    <table>
        <thead><tr><th>Evento</th><th>Total</th></tr></thead>
        <tbody>
            @forelse ($auditoriasPorEvento as $item)
                <tr><td>{{ $item->evento }}</td><td>{{ $item->total }}</td></tr>
            @empty
                <tr><td colspan="2">Sem dados</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <h3>Últimos Eventos de Auditoria (30)</h3>
    <table class="small">
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Usuário</th>
                <th>Evento</th>
                <th>Entidade</th>
                <th>Registro</th>
                <th>Instituição</th>
                <th>Região</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($auditoriasRecentes as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $item->usuario_nome }}</td>
                    <td>{{ strtoupper($item->event ?? '-') }}</td>
                    <td>{{ class_basename((string) $item->auditable_type) }}</td>
                    <td>{{ $item->auditable_id }}</td>
                    <td>{{ $item->instituicao_nome }}</td>
                    <td>{{ $item->regiao_nome }}</td>
                    <td>{{ $item->ip_address ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Sem dados</td></tr>
            @endforelse
        </tbody>
    </table>
    <script type="text/php">
        if (isset($pdf)) {
            $x = 760;
            $y = 574;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 8;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.40, 0.40, 0.40));
        }
    </script>
</body>
</html>
