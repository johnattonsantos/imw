<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait QuantidadeMembrosUtils
{
	public static function fetch($dataInicial, $dataFinal, $distritoId, $regiaoId = null): Collection
	{
		$results = DB::table('instituicoes_instituicoes as ii')
			->select('ii.id', 'ii.nome', 'dist.nome as distrito')
			->selectRaw("
				COUNT(CASE
					WHEN mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mr.id
					ELSE NULL
				END) AS total_ate_datainicial,
				COUNT(CASE
					WHEN mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mr.id
					ELSE NULL
					END) AS total_ate_datafinal
			")
			->leftJoin('membresia_rolpermanente as mr', function ($join) {
				$join->on('mr.igreja_id', '=', 'ii.id');
			})
			->leftJoin('instituicoes_instituicoes as dist', function ($join) {
				$join->on('ii.instituicao_pai_id', '=', 'dist.id')
					->where('dist.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO);
			})
			->when($distritoId == 'all' && $regiaoId, 
				fn ($query) => $query->whereIn('ii.instituicao_pai_id', Identifiable::fetchDistritosIdByRegiao($regiaoId)),
				fn ($query) => $query->where('ii.instituicao_pai_id', $distritoId),
			)->where('ii.tipo_instituicao_id',1)
			->groupBy('ii.id', 'ii.nome', 'dist.nome')
			->havingRaw('total_ate_datainicial > 0 OR total_ate_datafinal > 0')
			->orderBy('dist.nome', 'asc')
			->orderBy('ii.nome', 'asc')
			->get();
	
		return $results;
	}
}
