<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Relatorio Comunicação') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>{{ __('Relatorio de Comunicação') }}</h2>
    <p><strong>{{ __('Gerado em:') }}</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    <p><strong>{{ __('Busca:') }}</strong> {{ $search ?: 'Sem filtro' }}</p>
    <p><strong>{{ __('Total:') }}</strong> {{ $comunicacoes->count() }} registro(s)</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Categoria') }}</th>
                <th>{{ __('Título') }}</th>
                <th>{{ __('Comentário') }}</th>
                <th>{{ __('Arquivo') }}</th>
                <th>{{ __('Instituicao') }}</th>
                <th>{{ __('Criado em') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($comunicacoes as $comunicacao)
                <tr>
                    <td>{{ $comunicacao->id }}</td>
                    <td>{{ optional($comunicacao->categoria)->nome ?: '-' }}</td>
                    <td>{{ $comunicacao->titulo }}</td>
                    <td>{{ strip_tags($comunicacao->comentario) }}</td>
                    <td>{{ $comunicacao->arquivo ?: '-' }}</td>
                    <td>{{ optional($comunicacao->instituicao)->nome }}</td>
                    <td>{{ optional($comunicacao->created_at)->format('d/m/Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">{{ __('Nenhum registro encontrado.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
