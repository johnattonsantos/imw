<?php

namespace App\Exports;

use App\Services\ServiceRegiaoRelatorios\IdentificaDadosRegiaoRelatorioMembresiaService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;
class MembresiaExport implements FromCollection, WithHeadings, WithEvents, WithMapping, WithColumnFormatting
{
    protected $params;
    function __construct(array $params) {
        $this->params = $params;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = app(IdentificaDadosRegiaoRelatorioMembresiaService::class)->exportar($this->params);
        $membros = $data['membros'];
        return $membros;
    }

    // 1. Mapear e converter data para formato do Excel
    public function map($membros): array
    {
       // dd($membros);
        return [
            $membros->distrito_nome,
            $membros->igreja_nome,
            $membros->rol_atual,
            $membros->nome,
            $this->formatarTelefone($membros->telefone),
            $membros->status == 'I' ? 'Inativo' : 'Ativo',
            $membros->vinculo == 'M' ? 'Membro' : ($membros->vinculo == 'C' ? 'Congregado' : 'Visitante'),
            $this->formatDate($membros->data_nascimento),
            $this->formatDate($membros->dt_recepcao),
            $membros->recepcao_modo,
            $this->formatDate($membros->dt_exclusao),
            $membros->exclusao_modo,
            $membros->congregacao_nome ? $membros->congregacao_nome : 'SEDE', 
        ];
    }

    // 2. Definir a coluna 'C' (data) para o formato dd/mm/yyyy
    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function headings(): array
    {
        return [
            ['RELATÓRIO DE MEMBRESIA DA REGIÃO'],
            ['DISTRITO',
            'IGREJA',
            'ROL',
            'NOME',
            'TELEFONE',
            'SITUAÇÃO',
            'VÍNCULO',
            'NASCIMENTO',
            'RECEPÇÃO',
            'MODO',
            'EXCLUSÃO',
            'MODO',
            'LOCAL']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Mesclar A1 até C1 (ou o número de colunas que você tem)
                $sheet->mergeCells('A1:M1');

                // 2. Estilizar o título (A1)
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // 3. Estilizar o cabeçalho real (A2:C2)
                $sheet->getStyle('A2:M2')->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
            },
        ];
    }

    public function formatDate($date){
        if (empty($date)) {
            return '';
        }

        return Date::dateTimeToExcel(Carbon::parse($date));
    }

    function formatarTelefone($telefone)
    {
        if (empty($telefone)) {
            return '';
        }

        // Verificar se o número inclui o DDI (assumimos que o DDI tem 2 caracteres)
        if (strlen($telefone) >= 12) {
            $ddi = substr($telefone, 0, 2);  // Código do país
            $ddd = substr($telefone, 2, 2);  // Código de área (DDD)
            $numero = substr($telefone, 4);  // Número do telefone
        } else {
            $ddi = '';  // Sem código do país
            $ddd = substr($telefone, 0, 2);  // Código de área (DDD)
            $numero = substr($telefone, 2);  // Número do telefone
        }

        // Formatar número dependendo do seu comprimento
        if (strlen($numero) == 9) {
            // Número de celular
            $numero_formatado = substr($numero, 0, 5) . '-' . substr($numero, 5);
        } elseif (strlen($numero) == 8) {
            // Número de telefone fixo
            $numero_formatado = substr($numero, 0, 4) . '-' . substr($numero, 4);
        } else {
            // Formato desconhecido, exibir como está
            $numero_formatado = $numero;
        }

        if ($ddi) {
            return '+'.$ddi.' ('.$ddd.') '.$numero_formatado;
        } else {
            return '('.$ddd.') '.$numero_formatado;
        }
    }

}
