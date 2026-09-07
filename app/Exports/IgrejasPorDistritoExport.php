<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IgrejasPorDistritoExport implements FromArray, WithColumnWidths, WithEvents
{
    private array $rows = [];
    private array $rowTypes = [];

    public function __construct(Collection $igrejasPorDistrito, int $totalIgrejasRegiao)
    {
        foreach ($igrejasPorDistrito as $grupo) {
            $this->addRow(mb_strtoupper($grupo->distrito_nome), 'district');

            if ($grupo->igrejas->isEmpty()) {
                $this->addRow(__('Nenhuma igreja ativa'), 'church');
            } else {
                foreach ($grupo->igrejas as $igreja) {
                    $this->addRow(mb_strtoupper($igreja->igreja_nome), 'church');
                }
            }

            $this->addRow(__('Total do Distrito') . ': ' . $grupo->total, 'subtotal');
            $this->addRow('', 'blank');
        }

        $this->addRow(__('Total Geral da Região') . ': ' . $totalIgrejasRegiao, 'total');
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 48,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach ($this->rowTypes as $index => $type) {
                    $rowNumber = $index + 1;
                    $cell = 'A' . $rowNumber;

                    if ($type === 'blank') {
                        $sheet->getRowDimension($rowNumber)->setRowHeight(15);
                        continue;
                    }

                    $sheet->getStyle($cell)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => $type === 'church'
                                ? Alignment::HORIZONTAL_LEFT
                                : Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    if (in_array($type, ['district', 'subtotal', 'total'], true)) {
                        $sheet->getStyle($cell)->getFont()->setBold(true);
                    }

                    if ($type === 'district') {
                        $sheet->getStyle($cell)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFFF00'],
                            ],
                        ]);
                        $sheet->getRowDimension($rowNumber)->setRowHeight(24);
                    }

                    if (in_array($type, ['subtotal', 'total'], true)) {
                        $sheet->getStyle($cell)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2'],
                            ],
                        ]);
                    }
                }
            },
        ];
    }

    private function addRow(string $value, string $type): void
    {
        $this->rows[] = [$value];
        $this->rowTypes[] = $type;
    }
}
