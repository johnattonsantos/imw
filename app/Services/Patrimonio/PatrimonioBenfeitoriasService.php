<?php

namespace App\Services\Patrimonio;

use App\Models\Patrimonio\Imovel;

class PatrimonioBenfeitoriasService
{
    public function list(): array
    {
        return [];
    }

    public function aplicarDeltaValorHistorico(int $imovelId, float $delta): void
    {
        if ($imovelId <= 0 || $delta == 0.0) {
            return;
        }

        $imovel = Imovel::query()->find($imovelId);

        if (! $imovel) {
            return;
        }

        $novoValor = (float) $imovel->valor_historico + $delta;
        $imovel->valor_historico = max(0, round($novoValor, 2));
        $imovel->save();
    }
}
