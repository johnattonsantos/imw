<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Auditorias</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h2 { margin: 0 0 10px 0; }
        .meta { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        th { background: #f2f2f2; }
        .small { font-size: 9px; }
    </style>
</head>
<body>
    <h2>Auditorias do Sistema</h2>
    <div class="meta">
        <strong>Emitido em:</strong> {{ now()->format('d/m/Y H:i:s') }}<br>
        <strong>Total:</strong> {{ $audits->count() }} registro(s)
    </div>

    <table>
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Usuario</th>
                <th>Evento</th>
                <th>Entidade</th>
                <th>ID</th>
                <th>Alteracoes</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($audits as $audit)
                @php
                    $oldValues = is_array($audit->old_values) ? $audit->old_values : (json_decode($audit->old_values ?? '', true) ?: []);
                    $newValues = is_array($audit->new_values) ? $audit->new_values : (json_decode($audit->new_values ?? '', true) ?: []);
                    $campos = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                @endphp
                <tr>
                    <td>{{ optional($audit->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ optional($audit->user)->name ?? 'Sistema' }}</td>
                    <td>{{ strtoupper($audit->event) }}</td>
                    <td>{{ class_basename($audit->auditable_type) }}</td>
                    <td>{{ $audit->auditable_id }}</td>
                    <td class="small">{{ implode(', ', $campos) ?: '-' }}</td>
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
