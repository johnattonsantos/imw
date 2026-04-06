<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Facades\DB;

trait MembrosMinisterioUtils
{
    public static function fetch($dataInicial, $dataFinal, $tipo, $distritoId, $regiaoId = null)
    {
        $vinculoCondition = $tipo === 'C' ? ['C', 'M'] : ['M'];

        $results = DB::table('instituicoes_instituicoes as ii')
            ->select('ii.nome', 'dist.nome as distrito')
            ->selectRaw("
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 0 AND 9 
                        AND mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mm.id
                    ELSE NULL
                END) AS Kids_X,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 0 AND 9 
                        AND mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mm.id
                    ELSE NULL
                END) AS Kids_Y,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 10 AND 13 
                        AND mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mm.id
                    ELSE NULL
                END) AS Conexao_X,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 10 AND 13 
                        AND mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mm.id
                    ELSE NULL
                END) AS Conexao_Y,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 14 AND 17 
                        AND mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mm.id
                    ELSE NULL
                END) AS Fire_X,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 14 AND 17 
                        AND mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mm.id
                    ELSE NULL
                END) AS Fire_Y,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 18 AND 29 
                        AND mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mm.id
                    ELSE NULL
                END) AS Move_X,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 18 AND 29 
                        AND mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mm.id
                    ELSE NULL
                END) AS Move_Y,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 30 AND 59 
                        AND mm.sexo = 'M' 
                        AND mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mm.id
                    ELSE NULL
                END) AS Homens_X,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 30 AND 59 
                        AND mm.sexo = 'M' 
                        AND mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mm.id
                    ELSE NULL
                END) AS Homens_Y,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 30 AND 59 
                        AND mm.sexo = 'F' 
                        AND mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mm.id
                    ELSE NULL
                END) AS Mulheres_X,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) BETWEEN 30 AND 59 
                        AND mm.sexo = 'F' 
                        AND mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mm.id
                    ELSE NULL
                END) AS Mulheres_Y,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) >= 60 
                        AND mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mm.id
                    ELSE NULL
                END) AS Sessenta_X,
                COUNT(CASE 
                    WHEN TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE()) >= 60 
                        AND mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mm.id
                    ELSE NULL
                END) AS Sessenta_Y
            ")
            ->leftJoin('membresia_membros as mm', function ($join) use ($vinculoCondition) {
                $join->on('ii.id', '=', 'mm.igreja_id')
                    ->whereIn('mm.vinculo', $vinculoCondition);
            })
            ->leftJoin('membresia_rolpermanente as mr', function ($join) {
                $join->on('mr.membro_id', '=', 'mm.id');
            })
            ->leftJoin('instituicoes_instituicoes as dist', function ($join) {
				$join->on('ii.instituicao_pai_id', '=', 'dist.id')
					->where('dist.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
                    ->where('dist.ativo', 1);
			})
            ->when($distritoId == 'all' && $regiaoId,
                fn ($query) => $query->whereIn('ii.instituicao_pai_id', Identifiable::fetchDistritosIdByRegiao($regiaoId)),
                fn ($query) => $query->where('ii.instituicao_pai_id', $distritoId)
            )
            ->groupBy('ii.nome', 'dist.nome')
            ->orderBy('dist.nome', 'asc')
            ->orderBy('ii.nome', 'asc')
            ->get();

        return $results;
    }
}