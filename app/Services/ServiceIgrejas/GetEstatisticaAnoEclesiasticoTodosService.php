<?php

namespace App\Services\ServiceIgrejas;

use App\Models\InstituicoesInstituicao;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetEstatisticaAnoEclesiasticoTodosService
{
    public function execute(array $params = [])
    {
        $getIgrejas = app(GetIgrejas::class)->execute();
    
        $membresias = [];
        foreach($getIgrejas as $igreja){
            //$ano = isset($params['ano']) ? $params['ano'] : date('Y');
            //$dataReferencia = $this->handleDataReferencia($ano);
            $membrosRecebidos =  $this->handleEstatisticaRecepcao($igreja->id, $params);            
            $membrosExcluidos =  $this->handleEstatisticaExclusao($igreja->id, $params);
            $rolAtual         =  $this->handleTotalRolAtual($igreja->id, $params);
            $rolAnterior      =  $this->handleTotalRolAnterior($rolAtual, $membrosRecebidos, $membrosExcluidos);
            $membresias [] = [
                'igreja'           =>  $igreja,
                //'ano'              =>  $ano,
                'membrosRecebidos' =>  $membrosRecebidos,
                'membrosExcluidos' =>  $membrosExcluidos,
                'rolAtual'         =>  $rolAtual,
                'rolAnterior'      =>  $rolAnterior,
            ];
        }
        return $membresias;        
    }

    private function handleDataReferencia($ano): string
    {
        $dataHoje = Carbon::today()->startOfDay();
        $dataReferencia = Carbon::parse("$ano-11-01")->startOfDay();

        return $dataHoje->lessThan($dataReferencia)
            ? sprintf('%s-11-01', $ano - 2)
            : sprintf('%s-11-01', $ano);
    }

    private function handleEstatisticaRecepcao($igrejaId, $params)
    {
        $dataInicial = isset($params['data_inicial']) ? $params['data_inicial'] : '';
        $dataFinal = isset($params['data_final']) ? $params['data_final'] : '';
        $query = "SELECT 
            descricao,
            
            (	SELECT count(*) 
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mr.membro_id = mm.id
                AND mr.modo_recepcao_id = ms.id
                AND dt_recepcao BETWEEN '$dataInicial' AND '$dataFinal'
                AND mr.igreja_id = $igrejaId
                AND mm.sexo = 'M'	) sexo_masculino,
                
            (	SELECT count(*) 
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mr.membro_id = mm.id
                AND mr.modo_recepcao_id = ms.id
                AND dt_recepcao BETWEEN '$dataInicial' AND '$dataFinal'
                AND mr.igreja_id = $igrejaId
                AND mm.sexo = 'F') sexo_feminino,
                
            (	SELECT count(*) 
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mr.membro_id = mm.id
                AND dt_recepcao BETWEEN '$dataInicial' AND '$dataFinal'
                AND mr.igreja_id = $igrejaId
                AND mr.modo_recepcao_id = ms.id
            ) total
            
         FROM membresia_situacoes ms
        WHERE ms.tipo  = 'R'
          AND deleted_at IS NULL";

        return collect(DB::select($query));
    }

    private function handleEstatisticaExclusao($igrejaId, $params)
    {
        $dataInicial = isset($params['data_inicial']) ? $params['data_inicial'] : '';
        $dataFinal = isset($params['data_final']) ? $params['data_final'] : '';
        $query = "SELECT 
            ms.id AS situacao_id,
            descricao,
            
            (	SELECT count(*) 
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mr.membro_id = mm.id
                AND mr.modo_exclusao_id = ms.id
                AND mr.dt_exclusao BETWEEN '$dataInicial' AND '$dataFinal'
                AND mr.igreja_id = $igrejaId
                AND mm.sexo = 'M'	) sexo_masculino,
                
            (	SELECT count(*) 
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mr.membro_id = mm.id
                AND mr.modo_exclusao_id = ms.id
                AND mr.dt_exclusao BETWEEN '$dataInicial' AND '$dataFinal'
                AND mr.igreja_id = $igrejaId
                AND mm.sexo = 'F') sexo_feminino,
                
            (	SELECT count(*) 
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mr.membro_id = mm.id
                AND mr.dt_exclusao BETWEEN '$dataInicial' AND '$dataFinal'
                AND mr.igreja_id = $igrejaId
                AND mr.modo_exclusao_id = ms.id
            ) total
            
         FROM membresia_situacoes ms
        WHERE ms.tipo  = 'E'
          AND deleted_at IS NULL";

        return collect(DB::select($query));
    }

    private function handleTotalRolatual($igrejaId, $params)
    {
        $dataInicial = isset($params['data_inicial']) ? $params['data_inicial'] : '';
        $dataFinal = isset($params['data_final']) ? $params['data_final'] : '';
        $query = "SELECT 
            (	    
                SELECT count(*)
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mm.id = mr.membro_id 
                AND mr.dt_recepcao <= '$dataFinal'
                AND (mr.dt_exclusao > '$dataFinal' OR mr.dt_exclusao IS NULL)
                AND mr.igreja_id = $igrejaId
                AND mm.sexo = 'M'  
            ) sexo_masculino,  
            (	
                SELECT count(*)
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mm.id = mr.membro_id 
                AND mr.dt_recepcao <= '$dataFinal'
                AND (mr.dt_exclusao > '$dataFinal' OR mr.dt_exclusao IS NULL)
                AND mr.igreja_id = $igrejaId
                AND mm.sexo = 'F' 
                


            ) sexo_feminino,
            (
                SELECT count(*)
                FROM membresia_rolpermanente mr, membresia_membros mm 
                WHERE mm.id = mr.membro_id 
                AND mr.dt_recepcao <= '$dataFinal'
                AND (mr.dt_exclusao > '$dataFinal' OR mr.dt_exclusao IS NULL)
                AND mr.igreja_id = $igrejaId
            ) total";

        return DB::selectOne($query);
    }

    private function handleTotalRolAnterior(object $rolAtual, Collection $membrosRecebidos, Collection $membrosExcluidos)
    {
        return (object) [
            'sexo_masculino' => $rolAtual->sexo_masculino - $membrosRecebidos->sum('sexo_masculino') + $membrosExcluidos->sum('sexo_masculino'),
            'sexo_feminino'  => $rolAtual->sexo_feminino  - $membrosRecebidos->sum('sexo_feminino')  + $membrosExcluidos->sum('sexo_feminino'),
            'total'          => $rolAtual->total          - $membrosRecebidos->sum('total')          + $membrosExcluidos->sum('total'),
        ];
    }
}