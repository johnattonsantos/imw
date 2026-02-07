<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ComunicacaoExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $comunicacoes)
    {
    }

    public function collection(): Collection
    {
        return $this->comunicacoes;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Titulo',
            'Comentario',
            'Arquivo',
            'Instituicao',
            'Criado em',
        ];
    }

    public function map($comunicacao): array
    {
        return [
            $comunicacao->id,
            $comunicacao->titulo,
            strip_tags($comunicacao->comentario),
            $comunicacao->arquivo,
            optional($comunicacao->instituicao)->nome,
            optional($comunicacao->created_at)->format('d/m/Y H:i:s'),
        ];
    }
}
