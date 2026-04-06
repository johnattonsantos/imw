<?php

namespace App\Traits;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait QuantidadeMembrosUtils
{
	public static function fetch($dataInicial, $dataFinal, $tipo, $distritoId, $regiaoId = null): Collection
	{
		$vinculoCondition = $tipo === 'C' ? ['C', 'M'] : ['M'];
		$results = DB::table('instituicoes_instituicoes as ii')
			->select('ii.id', 'ii.nome', 'dist.nome as distrito')
			->selectRaw("
				COUNT(CASE
					WHEN mm.vinculo='M' and mr.dt_recepcao <= '{$dataInicial}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataInicial}') THEN mm.id
					when mm.vinculo='C' and mm.status='A' then mm.id
					ELSE NULL
				END) AS total_ate_datainicial,
				COUNT(CASE
					WHEN mm.vinculo='M' and mr.dt_recepcao <= '{$dataFinal}' AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > '{$dataFinal}') THEN mm.id
					when mm.vinculo='C' and mm.status='A' then mm.id
					ELSE NULL
				END) AS total_ate_datafinal
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
					->where('dist.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO);
			})
			->when($distritoId == 'all' && $regiaoId, 
				fn ($query) => $query->whereIn('ii.instituicao_pai_id', Identifiable::fetchDistritosIdByRegiao($regiaoId)),
				fn ($query) => $query->where('ii.instituicao_pai_id', $distritoId),
			)->where('ii.ativo',1)
			->groupBy('ii.id', 'ii.nome', 'dist.nome')
			->orderBy('dist.nome', 'asc')
			->orderBy('ii.nome', 'asc')
			->get();
	
		return $results;
	}
}