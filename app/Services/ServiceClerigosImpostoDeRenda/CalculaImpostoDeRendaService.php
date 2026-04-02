<?php

namespace App\Services\ServiceClerigosImpostoDeRenda;

use App\Calculators\ImpostoDeRenda\ImpostoDeRendaCalculatorInterface;
use App\Dtos\ImpostoDeRenda\ResponseIrDto;
use App\Models\DeducaoIr;
use App\Models\PessoasDependente;
use App\Models\PessoasPrebenda;
use App\Models\TabelaIr;

class CalculaImpostoDeRendaService
{
    public function __construct(private ImpostoDeRendaCalculatorInterface $irCalculator) {}

    public function execute(PessoasPrebenda $prebenda): ResponseIrDto
    {

        if (!$prebenda) {
            throw new \Exception('Prebenda não encontrada.');
        }

        $TabelaIr = TabelaIr::where('ano', $prebenda->ano)->get();
        $DeducaoIr = DeducaoIr::where('ano', $prebenda->ano)->where('simplificado', true)->first();

        if ($TabelaIr->isEmpty() && !$DeducaoIr) {
            throw new \Exception('A prebenda de ' . $prebenda->ano . ' ainda não esta parametrizada no sistema ');
        };


        $qtdDependentes = PessoasDependente::where('pessoa_id', $prebenda->pessoa_id)
            ->where('declarar_em_irpf', 1)
            ->count();

        return $this->irCalculator->calculate($prebenda->ano, $prebenda->valor, $qtdDependentes);
    }
}
