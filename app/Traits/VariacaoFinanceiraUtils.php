<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait VariacaoFinanceiraUtils
{
    public static function fetch($dataInicial, $dataFinal, $distritoId)
    {
        // Definir $dataInicial e $dataFinal com formato 'm/Y'
        $dataInicial = empty($dataInicial) ? Carbon::now()->format('m/Y') : $dataInicial;
        $dataFinal = empty($dataFinal) ? Carbon::now()->format('m/Y') : $dataFinal;

        // Extrair ano e mês das datas
        list($mesInicial, $anoInicial) = explode('/', $dataInicial);
        list($mesFinal, $anoFinal) = explode('/', $dataFinal);

        // Converter para inteiros
        $anoInicial = (int) $anoInicial;
        $mesInicial = (int) $mesInicial;
        $anoFinal = (int) $anoFinal;
        $mesFinal = (int) $mesFinal;

        // Montar a consulta SQL bruta
        $sql = "SELECT
                ii.id,
                ii.nome AS instituicao_nome,
                (SELECT IFNULL(fscm_sub.saldo_final, 0.0) 
                    FROM financeiro_saldo_consolidado_mensal fscm_sub 
                    WHERE fscm_sub.instituicao_id = ii.id 
                    AND ((fscm_sub.ano = :anoInicial AND fscm_sub.mes < :mesInicial) OR fscm_sub.ano < :anoInicial2)
                    ORDER BY fscm_sub.ano DESC, fscm_sub.mes DESC 
                    LIMIT 1) AS saldo_anterior,
                IFNULL(SUM(fscm.total_entradas), 0.0) AS total_entradas,
                IFNULL(SUM(fscm.total_transf_entradas), 0.0) AS total_transf_entradas,
                IFNULL(SUM(fscm.total_saidas), 0.0) AS total_saidas,
                IFNULL(SUM(fscm.total_transf_saidas), 0.0) AS total_transf_saidas,
                IFNULL(SUM(fscm.saldo_final), 0.0) AS saldo_final
            FROM instituicoes_instituicoes ii
            LEFT JOIN financeiro_saldo_consolidado_mensal fscm 
                ON ii.id = fscm.instituicao_id 
                AND fscm.ano = :ano 
                AND fscm.mes BETWEEN :mesInicial3 AND :mesFinal
            WHERE ii.instituicao_pai_id = :instituicaoPaiId
                AND ii.tipo_instituicao_id = 1
            GROUP BY ii.id, ii.nome
            ORDER BY ii.nome;
            ";
        // Executar a consulta bruta
        $result = DB::select($sql, [
            'anoInicial' => $anoInicial,
            'mesInicial' => $mesInicial,
            'anoInicial2' => $anoInicial,
            'ano' => $anoInicial,
            'mesInicial3' => $mesInicial,
            'mesFinal' => $mesFinal,
            'instituicaoPaiId' => $distritoId
        ]);

        return $result;
    }
}
