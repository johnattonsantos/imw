<?php

namespace App\Traits;

use App\Models\FinanceiroCaixa;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait BalanceteUtils
{
    public static function handleListaCaixas($instituicaoId)
    {
        return FinanceiroCaixa::where('instituicao_id', $instituicaoId)
            ->orderBy('id', 'asc')
            ->get();
    }


    public static function handleLancamentos($dt_inicial, $dt_final, $caixaID, $instituicaoId)
    {
        // Usando Carbon para manipulação de datas
        $dataInicial = Carbon::createFromFormat('m/Y', $dt_inicial)->startOfMonth()->format('Y-m-d');
        $dataFinal = Carbon::createFromFormat('m/Y', $dt_final)->endOfMonth()->format('Y-m-d');

        $tipoInstituicao = InstituicoesInstituicao::where('id', $instituicaoId)->first();
        
        $sql = "SELECT 
                    fpc.numeracao,
                    MAX(fpc.nome) AS nome,
                    fc.descricao AS caixa,
                    SUM(fl.valor) AS total
                FROM 
                    financeiro_lancamentos fl
                JOIN 
                    financeiro_plano_contas fpc ON fpc.id = fl.plano_conta_id
                JOIN 
                    financeiro_caixas fc ON fc.id = fl.caixa_id
                JOIN
                    instituicoes_instituicoes ii on ii.id = fl.instituicao_id AND ii.tipo_instituicao_id = $tipoInstituicao->tipo_instituicao_id
                WHERE 
                    fl.instituicao_id = '$instituicaoId' ";
        if ($caixaID !== 'all') {
            $sql .= "AND fc.id = '$caixaID' ";
        }
        $sql .= "AND fl.deleted_at IS NULL AND fl.conciliado = 1 
                AND fl.data_movimento BETWEEN '$dataInicial' AND '$dataFinal'
                GROUP BY 
                    fc.descricao,
                    CAST(SUBSTRING_INDEX(fpc.numeracao, '.', 1) AS UNSIGNED),  
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 2), '.', -1) AS UNSIGNED),   
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 3), '.', -1) AS UNSIGNED),  
                    fpc.numeracao   
                ORDER BY 
                    CAST(SUBSTRING_INDEX(fpc.numeracao, '.', 1) AS UNSIGNED),
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 2), '.', -1) AS UNSIGNED),
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 3), '.', -1) AS UNSIGNED),
                    fpc.numeracao";

        try {
            $lancamentos = DB::select($sql);
        } catch (\Exception $e) {
            throw $e;
        }

        return $lancamentos;

        // Usando Carbon para manipulação de datas
        // $dataInicial = Carbon::createFromFormat('m/Y', $dt_inicial)->startOfMonth()->format('Y-m-d');
        // $dataFinal = Carbon::createFromFormat('m/Y', $dt_final)->endOfMonth()->format('Y-m-d');

        // $sql = "SELECT 
        //             fpc.numeracao,
        //             MAX(fpc.nome) AS nome,
        //             fc.descricao AS caixa,
        //             SUM(fl.valor) AS total
        //         FROM 
        //             financeiro_lancamentos fl
        //         JOIN 
        //             financeiro_plano_contas fpc ON fpc.id = fl.plano_conta_id
        //         JOIN 
        //             financeiro_caixas fc ON fc.id = fl.caixa_id
        //         WHERE 
        //             fl.instituicao_id = '$instituicaoId' ";
        // if ($caixaID !== 'all') {
        //     $sql .= "AND fc.id = '$caixaID' ";
        // }
        // $sql .= "AND fl.deleted_at IS NULL 
        //         AND fl.data_movimento BETWEEN '$dataInicial' AND '$dataFinal'
        //         GROUP BY 
        //             fc.descricao,
        //             CAST(SUBSTRING_INDEX(fpc.numeracao, '.', 1) AS UNSIGNED),  
        //             CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 2), '.', -1) AS UNSIGNED),   
        //             CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 3), '.', -1) AS UNSIGNED),  
        //             fpc.numeracao   
        //         ORDER BY 
        //             CAST(SUBSTRING_INDEX(fpc.numeracao, '.', 1) AS UNSIGNED),
        //             CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 2), '.', -1) AS UNSIGNED),
        //             CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 3), '.', -1) AS UNSIGNED),
        //             fpc.numeracao";

        // try {
        //     $lancamentos = DB::select($sql);
        // } catch (\Exception $e) {
        //     throw $e;
        // }

        // return $lancamentos;
    }

    public static function handleLancamentosRegiao($dt_inicial, $dt_final, $caixaID, $instituicaoId)
    {
        // Usando Carbon para manipulação de datas
        $dataInicial = Carbon::createFromFormat('m/Y', $dt_inicial)->startOfMonth()->format('Y-m-d');
        $dataFinal = Carbon::createFromFormat('m/Y', $dt_final)->endOfMonth()->format('Y-m-d');
        $tipoIgreja = InstituicoesTipoInstituicao::IGREJA_LOCAL;

        $sql = "SELECT 
                    fpc.numeracao,
                    MAX(fpc.nome) AS nome,
                    SUM(fl.valor) AS total
                FROM 
                    financeiro_lancamentos fl
                JOIN 
                    financeiro_plano_contas fpc ON fpc.id = fl.plano_conta_id
                JOIN
                    instituicoes_instituicoes ii ON ii.id = fl.instituicao_id
                WHERE 
                    ii.tipo_instituicao_id = $tipoIgreja
                    AND fl.deleted_at IS NULL AND fl.conciliado = 1 ";
                    if ($instituicaoId) {
                        $sql .= "AND fl.instituicao_id = '$instituicaoId' ";
                    }
                $sql .= " AND fl.data_movimento BETWEEN '$dataInicial' AND '$dataFinal'
                GROUP BY 
                    CAST(SUBSTRING_INDEX(fpc.numeracao, '.', 1) AS UNSIGNED),  
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 2), '.', -1) AS UNSIGNED),   
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 3), '.', -1) AS UNSIGNED),  
                    fpc.numeracao   
                ORDER BY 
                    CAST(SUBSTRING_INDEX(fpc.numeracao, '.', 1) AS UNSIGNED),
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 2), '.', -1) AS UNSIGNED),
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(fpc.numeracao, '.', 3), '.', -1) AS UNSIGNED),
                    fpc.numeracao";

        try {
            $lancamentos = DB::select($sql);
        } catch (\Exception $e) {
            throw $e;
        }

        return $lancamentos;
    }


    public static function handleCaixas($dt_inicial, $dt_final, $caixaId, $instituicaoId)
    {
        // Usando Carbon para manipulação de datas
        $dataInicial = Carbon::createFromFormat('m/Y', $dt_inicial)->startOfMonth()->format('Y-m-d');
        $dataFinal = Carbon::createFromFormat('m/Y', $dt_final)->endOfMonth()->format('Y-m-d');

        // Calculando o mês anterior usando Carbon
        $dataMesAnterior = Carbon::createFromFormat('m/Y', $dt_inicial)->subMonth();
        $mesAnterior = $dataMesAnterior->format('m');
        $anoAnterior = $dataMesAnterior->format('Y');

        // Construção da query com as variáveis diretamente
        $sql = "SELECT 
            fc.descricao AS caixa, fc.id,
            COALESCE(MAX(fscm_max.saldo_final), 0.00) AS saldo_final,
            COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'E' THEN fl.valor ELSE 0 END), 0) AS total_entradas,
            COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'S' THEN fl.valor ELSE 0 END), 0) AS total_saidas,
            COALESCE(SUM(CASE WHEN fl.plano_conta_id IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'E' THEN fl.valor ELSE 0 END), 0) AS total_transferencias_entrada,
            COALESCE(SUM(CASE WHEN fl.plano_conta_id IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'S' THEN fl.valor ELSE 0 END), 0) AS total_transferencias_saida,
            COALESCE((
                (COALESCE(MAX(fscm_max.saldo_final), 0.00) +  
                COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'E' THEN fl.valor ELSE 0 END), 0) +
                COALESCE(SUM(CASE WHEN fl.plano_conta_id IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'E' THEN fl.valor ELSE 0 END), 0)
                ) -
                (
                COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'S' THEN fl.valor ELSE 0 END), 0) +
                COALESCE(SUM(CASE WHEN fl.plano_conta_id IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'S' THEN fl.valor ELSE 0 END), 0)
                )
            ), 0) AS saldo_atual
        FROM 
            financeiro_caixas fc
            LEFT JOIN 
            financeiro_lancamentos fl ON fc.id = fl.caixa_id AND fl.deleted_at IS NULL 
            AND fl.data_movimento BETWEEN '$dataInicial' AND '$dataFinal'
        LEFT JOIN 
        (
        SELECT 
                fscm_interno.caixa_id,
                fscm_interno.saldo_final
            FROM 
                financeiro_saldo_consolidado_mensal fscm_interno
            WHERE 
                fscm_interno.instituicao_id = '$instituicaoId'
                AND fscm_interno.ano = '$anoAnterior'
                AND fscm_interno.mes = '$mesAnterior'
        ) fscm_max on fc.id=fscm_max.caixa_id
        WHERE 
            fc.instituicao_id = '$instituicaoId'
            AND fc.deleted_at IS NULL ";

        if ($caixaId !== 'all') {
            $sql .= "AND fc.id = '$caixaId' ";
        }

        $sql .= "GROUP BY 
                fc.id,
                fc.descricao
            ORDER BY 
                fc.id";


        try {
            $caixas = DB::select($sql);
        } catch (\Exception $e) {
            throw $e;
        }

        return $caixas;
    }

    public static function handleCaixasRegiao($dt_inicial, $dt_final, $caixaId, $instituicaoId)
    {
        //dd($instituicaoId);
        $dataIni = explode('/',$dt_inicial);
        $tdInicial = $dataIni[1].$dataIni[0];
       
        $dataFin = explode('/',$dt_final); 
        $tdFinal  = $dataFin[1].$dataFin[0];
        // Usando Carbon para manipulação de datas
        $dataInicial = Carbon::createFromFormat('m/Y', $dt_inicial)->startOfMonth()->format('Y-m-d');
        $dataFinal = Carbon::createFromFormat('m/Y', $dt_final)->endOfMonth()->format('Y-m-d');

        // Calculando o mês anterior usando Carbon
        $dataMesAnterior = Carbon::createFromFormat('m/Y', $dt_inicial)->subMonth();
        $mesAnterior = $dataMesAnterior->format('m');
        $anoAnterior = $dataMesAnterior->format('Y');

        // Construção da query com as variáveis diretamente
        $tipoIgreja = InstituicoesTipoInstituicao::IGREJA_LOCAL;
        $filtroIgreja = " AND EXISTS (
                            SELECT 1
                            FROM instituicoes_instituicoes ii
                            WHERE ii.id = fscm.instituicao_id
                              AND ii.tipo_instituicao_id = $tipoIgreja
                              AND ii.deleted_at IS NULL
                        )";

        $sql = "SELECT 'saldo_inicial', sum(fscm.saldo_anterior ) AS saldo
                    FROM financeiro_saldo_consolidado_mensal fscm
                    WHERE fscm.ano=$dataIni[1] AND fscm.mes=$dataIni[0] $filtroIgreja";
                    if ($instituicaoId) {
                        $sql .= " AND fscm.instituicao_id = '$instituicaoId' ";
                    }
        $sql .= " UNION 
                SELECT 'total_entradas', sum(fscm.total_entradas )
                    FROM financeiro_saldo_consolidado_mensal fscm
                    WHERE (fscm.ano * 100 + fscm.mes) between $tdInicial AND $tdFinal $filtroIgreja ";
                    if ($instituicaoId) {
                        $sql .= " AND fscm.instituicao_id = '$instituicaoId' ";
                    }
        $sql .= " UNION 
                SELECT 'total_saidas', sum(fscm.total_saidas )
                    FROM financeiro_saldo_consolidado_mensal fscm
                    WHERE (fscm.ano * 100 + fscm.mes) between $tdInicial AND $tdFinal $filtroIgreja";
                    if ($instituicaoId) {
                        $sql .= " AND fscm.instituicao_id = '$instituicaoId' ";
                    }
        $sql .= " UNION 
                SELECT 'total_tranf_entradas', sum(fscm.total_transf_entradas  )
                    FROM financeiro_saldo_consolidado_mensal fscm
                    WHERE (fscm.ano * 100 + fscm.mes) between $tdInicial AND $tdFinal $filtroIgreja";
                    if ($instituicaoId) {
                        $sql .= " AND fscm.instituicao_id = '$instituicaoId' ";
                    }
        $sql .= " UNION 
                SELECT 'total_transf_saidas', sum(fscm.total_transf_saidas  )
                    FROM financeiro_saldo_consolidado_mensal fscm
                    WHERE (fscm.ano * 100 + fscm.mes) between $tdInicial AND $tdFinal $filtroIgreja";
                    if ($instituicaoId) {
                        $sql .= " AND fscm.instituicao_id = '$instituicaoId' ";
                    }
        $sql .= " UNION 
                SELECT 'saldo_atual', sum(fscm.saldo_final )
                    FROM financeiro_saldo_consolidado_mensal fscm
                    WHERE fscm.ano=$dataFin[1] AND fscm.mes=$dataFin[0] $filtroIgreja";
                    if ($instituicaoId) {
                        $sql .= " AND fscm.instituicao_id = '$instituicaoId' ";
                    }

                   //var_dump($sql);die();
//dd($sql);


        // $sql = "SELECT (SELECT saldo_anterior FROM financeiro_saldo_consolidado_mensal ORDER BY id ASC LIMIT 1) as 'saldo_atual', sum(fscm.total_entradas) as 'total_entradas', sum(fscm.total_saidas) as 'total_saidas', sum(fscm.total_transf_entradas ) as 'total_transferencias_entrada', sum(fscm.total_transf_saidas ) as 'total_transferencias_saida', (SELECT saldo_final FROM financeiro_saldo_consolidado_mensal ORDER BY id ASC LIMIT 1) as 'saldo_final' from financeiro_saldo_consolidado_mensal fscm WHERE (fscm.ano * 100 + fscm.mes) BETWEEN $tdInicial AND $tdFinal  and fscm.deleted_at is null
        // ";
        //var_dump($sql);die();
        // $sql = "SELECT 
        //     COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137)  THEN fscm.saldo_final ELSE 0 END), 0) AS saldo_final,
        //     COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'E' THEN fl.valor ELSE 0 END), 0) AS total_entradas,
        //     COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'S' THEN fl.valor ELSE 0 END), 0) AS total_saidas,
        //     COALESCE(SUM(CASE WHEN fl.plano_conta_id IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'E' THEN fl.valor ELSE 0 END), 0) AS total_transferencias_entrada,
        //     COALESCE(SUM(CASE WHEN fl.plano_conta_id IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'S' THEN fl.valor ELSE 0 END), 0) AS total_transferencias_saida,
        //     COALESCE(( (COALESCE(MAX(fscm.saldo_final), 0.00) + COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'E' THEN fl.valor ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN fl.plano_conta_id IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'E' THEN fl.valor ELSE 0 END), 0) ) - ( COALESCE(SUM(CASE WHEN fl.plano_conta_id NOT IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'S' THEN fl.valor ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN fl.plano_conta_id IN (100002, 100003, 110095, 110096, 110097, 110098, 110120, 110121, 110128, 110137) AND fl.tipo_lancamento = 'S' THEN fl.valor ELSE 0 END), 0) ) ), 0) AS saldo_atual 
        //     FROM financeiro_saldo_consolidado_mensal fscm
        //     JOIN financeiro_lancamentos fl ON fl.caixa_id = fscm.caixa_id AND fl.deleted_at IS NULL AND fl.data_movimento BETWEEN '$dataInicial' AND '$dataFinal'"; 
            
        try {
            $caixas = (array) DB::select($sql);
        } catch (\Exception $e) {
            throw $e;
        }
        //dd((array) $caixas['2']);
        return $caixas;
    }


    public static function handleListaIgrejasByRegiao($regiaoId)
    {
        $distritos = InstituicoesInstituicao::where('instituicao_pai_id', $regiaoId)
            ->get()
            ->unique('id')
            ->pluck('id')
            ->toArray();

        return DB::table('instituicoes_instituicoes as ii')
            ->join('instituicoes_tiposinstituicao as it', 'it.id', '=', 'ii.tipo_instituicao_id')
            ->select('ii.id', 'ii.nome as descricao')
            ->whereIn('ii.instituicao_pai_id', $distritos)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('ii.ativo', 1)
            ->whereNull('ii.deleted_at')
            ->orderBy('ii.nome')
            ->get();
    }
}
