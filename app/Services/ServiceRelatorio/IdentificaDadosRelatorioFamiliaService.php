<?php

namespace App\Services\ServiceRelatorio;

use App\Models\MembresiaMembro;
use App\Traits\Identifiable;

class IdentificaDadosRelatorioFamiliaService
{
    use Identifiable;

    public function execute(): array
    {
        return [
            'membros' => MembresiaMembro::query()
                ->with('familiar')
                ->where('igreja_id', self::fetchSessionIgrejaLocal()->id)
                ->where('vinculo', MembresiaMembro::VINCULO_MEMBRO)
                ->where('status', MembresiaMembro::STATUS_ATIVO)
                ->orderBy('nome')
                ->get(),
        ];
    }
}
