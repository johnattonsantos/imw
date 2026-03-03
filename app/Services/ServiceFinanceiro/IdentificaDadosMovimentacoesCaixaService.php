<?php

namespace App\Services\ServiceFinanceiro;

use App\Traits\FinanceiroUtils;
use App\Models\FinanceiroLancamento;
use App\Models\InstituicoesInstituicao;
use Carbon\Carbon;

class IdentificaDadosMovimentacoesCaixaService
{
    use FinanceiroUtils;

    public function execute($filters = [])
    {
        return [
            'planoContas' => FinanceiroUtils::planoContas(),
            'caixas'      => FinanceiroUtils::caixas(),
            'lancamentos' => FinanceiroUtils::lancamentos($filters)
        ];
    }
}
