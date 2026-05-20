<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h2 { margin: 0 0 4px; }
        .meta { margin-bottom: 14px; color: #444; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h2>{{ $report['title'] }}</h2>
    <div class="meta">
        <div><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</div>
        <div><strong>Total de registros:</strong> {{ number_format(count($report['rows']), 0, ',', '.') }}</div>
        <div><strong>Filtros:</strong>
            Igreja/Unidade: {{ $filters['igreja_id'] ?? 'todas' }} |
            Categoria: {{ $filters['categoria'] ?? 'todas' }} |
            Status: {{ $filters['status'] ?? 'todos' }} |
            Período: {{ $filters['periodo_inicio'] ?? '-' }} até {{ $filters['periodo_fim'] ?? '-' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($report['headings'] as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($report['rows'] as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($report['headings']) }}">Nenhum dado encontrado para os filtros informados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
