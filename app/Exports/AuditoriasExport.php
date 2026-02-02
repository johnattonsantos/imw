<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuditoriasExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $audits)
    {
    }

    public function collection(): Collection
    {
        return $this->audits;
    }

    public function headings(): array
    {
        return [
            'Data/Hora',
            'Usuario',
            'Email',
            'Evento',
            'Entidade',
            'Registro ID',
            'IP',
            'URL',
            'Antes',
            'Depois',
        ];
    }

    public function map($audit): array
    {
        return [
            optional($audit->created_at)->format('d/m/Y H:i:s'),
            optional($audit->user)->name ?? 'Sistema',
            optional($audit->user)->email,
            strtoupper((string) $audit->event),
            class_basename((string) $audit->auditable_type),
            $audit->auditable_id,
            $audit->ip_address,
            $audit->url,
            json_encode($this->normalizeAuditValues($audit->old_values), JSON_UNESCAPED_UNICODE),
            json_encode($this->normalizeAuditValues($audit->new_values), JSON_UNESCAPED_UNICODE),
        ];
    }

    private function normalizeAuditValues($values): array
    {
        if (is_array($values)) {
            return $values;
        }

        if (is_string($values) && $values !== '') {
            $decoded = json_decode($values, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
