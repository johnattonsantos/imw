<?php

namespace App\Services\ServiceContabilidade;

use App\Models\Mes;
use App\Models\PessoasPrebenda;
use App\Calculators\ImpostoDeRenda\ImpostoDeRendaSimplificadoCalculator;
use App\Models\DeducaoIr;
use App\Models\TabelaIr;
use App\Services\ServiceClerigosImpostoDeRenda\CalculaImpostoDeRendaService;
use App\Traits\ContabilidadeDados;
use Carbon\Carbon;

class IrrfServices
{
    public function execute(array $params = [])
    {
        $data['anos'] =  ContabilidadeDados::fetchAnos();
        $data['meses'] =  ContabilidadeDados::fetchMeses();
        if(isset($params['ano'])) {
            $mes =  ContabilidadeDados::fetchMes($params);
            $prebendasAll = ContabilidadeDados::fetchPrebandas($params);
            $prebendas = [];
            foreach($prebendasAll as $item){
                if($item->id){
                    $prebenda = PessoasPrebenda::where('id', $item->id)->first();
                    $irCalculator = new ImpostoDeRendaSimplificadoCalculator();
                    $impostoCalculado = (new CalculaImpostoDeRendaService($irCalculator))->execute($prebenda);
                }else{
                    $impostoCalculado = (object)[
                        'ano' => $params['ano'],
                        'rendimentosTributaveis' => 0,
                        'qtdeDependentes' => 0,
                        'valorDedutivel' => 0,
                        'valorBase' => 0,
                        'valorRedutor' => 0,
                        'impostoSemDeducao' => 0,
                        'valorImposto' => 0,
                        'progressao' => []
                    ];
                }
                $prebendas[] = ['prebanda' => $item, 'imposto' => $impostoCalculado];
            }
            $data['prebendas'] = (object) $prebendas;

            $data['titulo'] = "IMW - RELATÓRIO CONTABILIDADE IRRF {$params['ano']}/$mes ";
        }else{
            $data['titulo'] = "";
        }
        
        return $data;
    }
}