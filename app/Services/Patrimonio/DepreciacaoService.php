<?php

namespace App\Services\Patrimonio;

use App\Models\Patrimonio\BemMovel;
use Illuminate\Support\Carbon;

class DepreciacaoService
{
    public function calcular(BemMovel $bemMovel, ?Carbon $dataBase = null): array
    {
        $dataBase = ($dataBase ?: now())->copy()->startOfDay();

        $valorAquisicao = max(0, (float) ($bemMovel->valor_aquisicao ?? 0));
        $valorResidual = max(0, (float) ($bemMovel->valor_residual ?? 0));
        $valorResidual = min($valorResidual, $valorAquisicao);

        $vidaUtilAnos = max(0, (int) ($bemMovel->vida_util ?? 0));
        $baseDepreciavel = max(0, $valorAquisicao - $valorResidual);

        $depreciacaoAnual = ($vidaUtilAnos > 0 && $baseDepreciavel > 0)
            ? $baseDepreciavel / $vidaUtilAnos
            : 0.0;

        $anosDecorridos = 0.0;

        if ($vidaUtilAnos > 0 && ! empty($bemMovel->data_aquisicao)) {
            $dataAquisicao = $bemMovel->data_aquisicao->copy()->startOfDay();

            if ($dataAquisicao->lessThanOrEqualTo($dataBase)) {
                $anosDecorridos = min($vidaUtilAnos, $dataAquisicao->diffInDays($dataBase) / 365);
            }
        }

        $depreciacaoAcumulada = min($baseDepreciavel, max(0, $depreciacaoAnual * $anosDecorridos));
        $valorContabilAtual = max($valorResidual, $valorAquisicao - $depreciacaoAcumulada);

        $percentualDepreciado = $baseDepreciavel > 0
            ? min(100, ($depreciacaoAcumulada / $baseDepreciavel) * 100)
            : 0.0;

        $statusDepreciado = $vidaUtilAnos > 0
            && $baseDepreciavel > 0
            && ($anosDecorridos >= $vidaUtilAnos || abs($valorContabilAtual - $valorResidual) < 0.01);

        return [
            'depreciacao_anual' => round($depreciacaoAnual, 2),
            'depreciacao_acumulada' => round($depreciacaoAcumulada, 2),
            'valor_contabil_atual' => round($valorContabilAtual, 2),
            'percentual_depreciado' => round($percentualDepreciado, 2),
            'status_depreciado' => $statusDepreciado,
            'anos_decorridos' => round($anosDecorridos, 2),
            'vida_util_anos' => $vidaUtilAnos,
        ];
    }

    public function aplicarNoBemMovel(BemMovel $bemMovel, ?Carbon $dataBase = null): array
    {
        $dados = $this->calcular($bemMovel, $dataBase);

        if ($dados['status_depreciado'] && $bemMovel->status !== 'depreciado') {
            $bemMovel->forceFill(['status' => 'depreciado'])->saveQuietly();
            $bemMovel->status = 'depreciado';
        }

        return $dados;
    }
}
