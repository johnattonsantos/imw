<?php

namespace App\Services\ServiceContabilidade;

use App\Models\InstituicoesTipoInstituicao;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class IrrfPorIgrejaService
{
    public function listar(int $regiaoId, string $inicio, string $fim): Collection
    {
        // Aggregate by the institution that owns the entry, not the clergy's appointment.
        $movimentos = DB::table('financeiro_lancamentos as fl')
            ->leftJoin('pessoas_pessoas as clerigo', 'clerigo.id', '=', 'fl.clerigo_id')
            ->where('fl.conciliado', 1)
            ->whereNull('fl.deleted_at')
            ->whereBetween('fl.data_movimento', [$inicio, $fim])
            ->whereIn('fl.plano_conta_id', [41, 220])
            ->select('fl.instituicao_id')
            ->selectRaw('SUM(CASE WHEN fl.plano_conta_id = 41 THEN fl.valor ELSE 0 END) as retido')
            ->selectRaw('SUM(CASE WHEN fl.plano_conta_id = 220 THEN fl.valor ELSE 0 END) as repassado')
            ->selectRaw('SUM(CASE WHEN fl.plano_conta_id = 41 AND clerigo.id IS NULL THEN fl.valor ELSE 0 END) as retido_sem_clerigo')
            ->selectRaw('SUM(CASE WHEN fl.plano_conta_id = 220 AND clerigo.id IS NULL THEN fl.valor ELSE 0 END) as repassado_sem_clerigo')
            ->selectRaw('COUNT(CASE WHEN clerigo.id IS NULL THEN fl.id END) as quantidade_sem_clerigo')
            ->groupBy('fl.instituicao_id');

        return DB::table('instituicoes_instituicoes as igreja')
            ->join('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->leftJoinSub($movimentos, 'irrf', 'irrf.instituicao_id', '=', 'igreja.id')
            ->where('distrito.instituicao_pai_id', $regiaoId)
            ->where('distrito.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('igreja.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->whereNull('igreja.deleted_at')
            ->whereNull('distrito.deleted_at')
            ->select('igreja.id', 'igreja.nome as igreja', 'distrito.nome as distrito')
            ->selectRaw('COALESCE(irrf.retido, 0) as retido, COALESCE(irrf.repassado, 0) as repassado')
            ->selectRaw('COALESCE(irrf.retido, 0) - COALESCE(irrf.repassado, 0) as diferenca')
            ->selectRaw('COALESCE(irrf.retido_sem_clerigo, 0) as retido_sem_clerigo')
            ->selectRaw('COALESCE(irrf.repassado_sem_clerigo, 0) as repassado_sem_clerigo')
            ->selectRaw('COALESCE(irrf.quantidade_sem_clerigo, 0) as quantidade_sem_clerigo')
            ->orderByDesc('quantidade_sem_clerigo')
            ->orderBy('distrito.nome')->orderBy('igreja.nome')
            ->get();
    }
}
