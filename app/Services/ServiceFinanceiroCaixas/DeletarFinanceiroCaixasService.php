<?php

namespace App\Services\ServiceFinanceiroCaixas;

use App\Models\FinanceiroCaixa;
use App\Models\FinanceiroLancamento;
use App\Models\FinanceiroSaldoConsolidadoMensal;

class DeletarFinanceiroCaixasService
{
    public function execute($id)
    {
        $financeiroLancamento = FinanceiroLancamento::where('caixa_id', $id)->exists();
        if(!$financeiroLancamento){
            $financeiroSaldoConsolidadoMensal = FinanceiroSaldoConsolidadoMensal::where('caixa_id', $id)->where('ano', '>', 0)->where('mes', '>', 0)->exists();
            if(!$financeiroSaldoConsolidadoMensal){
                $hasLancamentos = false;
            }else{
                $hasLancamentos = true;
            }
        }else{
            $hasLancamentos = true;
        }
        if (!$hasLancamentos) {
            $caixa = FinanceiroCaixa::findOrFail($id);
            $caixa->delete();
        } else {
            throw new \Exception('Não é possível excluir o caixa pois existem lançamentos associados a ele.');
        }
    }
}
