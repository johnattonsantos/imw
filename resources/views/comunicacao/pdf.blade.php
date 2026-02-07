<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatorio Comunicacao</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Relatorio de Comunicacao</h2>
    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    <p><strong>Busca:</strong> {{ $search ?: 'Sem filtro' }}</p>
    <p><strong>Total:</strong> {{ $comunicacoes->count() }} registro(s)</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titulo</th>
                <th>Comentario</th>
                <th>Arquivo</th>
                <th>Instituicao</th>
                <th>Criado em</th>
            </tr>
        </thead>
        <tbody>
            @forelse($comunicacoes as $comunicacao)
                <tr>
                    <td>{{ $comunicacao->id }}</td>
                    <td>{{ $comunicacao->titulo }}</td>
                    <td>{{ strip_tags($comunicacao->comentario) }}</td>
                    <td>{{ $comunicacao->arquivo ?: '-' }}</td>
                    <td>{{ optional($comunicacao->instituicao)->nome }}</td>
                    <td>{{ optional($comunicacao->created_at)->format('d/m/Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
