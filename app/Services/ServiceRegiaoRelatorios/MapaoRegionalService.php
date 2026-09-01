<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\InstituicoesTipoInstituicao;
use App\Support\PeriodoEclesiastico;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MapaoRegionalService
{
    public function execute(): array
    {
        $regiao = Identifiable::fetchtSessionRegiao();
        $regiaoId = (int) $regiao->id;

        $distritoIds = $this->distritoIds($regiaoId);
        $igrejaIds = $this->igrejaIds($distritoIds);

        [$dataInicialPeriodo, $dataFinalPeriodo] = $this->periodoBienioCorrente();
        $mesesPeriodo = $this->mesesNoPeriodo($dataInicialPeriodo, $dataFinalPeriodo);
        $trimestresPeriodo = $this->trimestresNoPeriodo($dataInicialPeriodo, $dataFinalPeriodo);

        $totalArrecadacao = $this->totalArrecadacao($regiaoId, $distritoIds, $igrejaIds, $dataInicialPeriodo, $dataFinalPeriodo);
        $totalRecebimentos = $this->totalRolPermanente($regiaoId, 'A', 'dt_recepcao', $dataInicialPeriodo, $dataFinalPeriodo);
        $totalExclusoes = $this->totalRolPermanente($regiaoId, 'I', 'dt_exclusao', $dataInicialPeriodo, $dataFinalPeriodo);

        return [
            'regiao' => $regiao,
            'cards' => [
                ['titulo' => 'Total de membros', 'valor' => $this->totalMembrosQuantidadeMembros($regiaoId, $dataFinalPeriodo), 'tipo' => 'numero'],
                ['titulo' => 'Total de clérigos', 'valor' => $this->totalClerigos($regiaoId), 'tipo' => 'numero'],
                ['titulo' => 'Total de congregados', 'valor' => $this->totalCongregadosRelatorio($regiaoId), 'tipo' => 'numero'],
                ['titulo' => 'Total de distritos', 'valor' => count($distritoIds), 'tipo' => 'numero'],
                ['titulo' => 'Total de igrejas', 'valor' => count($igrejaIds), 'tipo' => 'numero'],
                ['titulo' => 'Total de congregações', 'valor' => $this->totalCongregacoes($regiaoId), 'tipo' => 'numero'],
                ['titulo' => 'Média da arrecadação mensal', 'valor' => $totalArrecadacao / $mesesPeriodo, 'tipo' => 'moeda'],
                ['titulo' => 'Total de GCEUs', 'valor' => $this->totalGceus($regiaoId), 'tipo' => 'numero'],
                ['titulo' => 'Total de integrantes de GCEUs', 'valor' => $this->totalIntegrantesGceus($igrejaIds), 'tipo' => 'numero'],
                ['titulo' => 'Total de ministérios', 'valor' => $this->totalMinisterios(), 'tipo' => 'numero'],
                ['titulo' => 'Total de integrantes nos ministérios', 'valor' => $this->totalIntegrantesMinisterios($regiaoId), 'tipo' => 'numero'],
                ['titulo' => 'Média de recebimento de membros no trimestre', 'valor' => $totalRecebimentos / $trimestresPeriodo, 'tipo' => 'decimal'],
                ['titulo' => 'Média de exclusões por trimestre', 'valor' => $totalExclusoes / $trimestresPeriodo, 'tipo' => 'decimal'],
            ],
            'periodos' => [
                'data_inicial' => $dataInicialPeriodo,
                'data_final' => $dataFinalPeriodo,
                'meses_periodo' => $mesesPeriodo,
                'trimestres_periodo' => $trimestresPeriodo,
                'descricao' => 'Biênio corrente',
            ],
        ];
    }

    private function periodoBienioCorrente(): array
    {
        return PeriodoEclesiastico::bienioCorrente();
    }

    private function mesesNoPeriodo(Carbon $dataInicial, Carbon $dataFinal): int
    {
        return max(1, $dataInicial->copy()->startOfMonth()->diffInMonths($dataFinal->copy()->startOfMonth()) + 1);
    }

    private function trimestresNoPeriodo(Carbon $dataInicial, Carbon $dataFinal): int
    {
        $trimestreInicial = ((int) $dataInicial->format('Y') * 4) + (int) ceil((int) $dataInicial->format('n') / 3);
        $trimestreFinal = ((int) $dataFinal->format('Y') * 4) + (int) ceil((int) $dataFinal->format('n') / 3);

        return max(1, $trimestreFinal - $trimestreInicial + 1);
    }

    private function distritoIds(int $regiaoId): array
    {
        return DB::table('instituicoes_instituicoes as ii')
            ->join('instituicoes_instituicoes as ip', 'ii.instituicao_pai_id', '=', 'ip.id')
            ->where('ip.tipo_instituicao_id', InstituicoesTipoInstituicao::REGIAO)
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('ip.id', $regiaoId)
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->pluck('ii.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function igrejaIds(array $distritoIds): array
    {
        if (empty($distritoIds)) {
            return [];
        }

        return DB::table('instituicoes_instituicoes as ii')
            ->join('instituicoes_instituicoes as ip', 'ii.instituicao_pai_id', '=', 'ip.id')
            ->where('ip.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where(function ($query) {
                $query->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_GERAL)
                    ->orWhere('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL);
            })
            ->whereIn('ii.instituicao_pai_id', $distritoIds)
            ->where('ip.ativo', 1)
            ->whereNull('ip.data_encerramento')
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->pluck('ii.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function totalMembrosQuantidadeMembros(int $regiaoId, Carbon $dataFinal): int
    {
        $distritoIds = Identifiable::fetchDistritosIdByRegiao($regiaoId);

        if (empty($distritoIds)) {
            return 0;
        }

        $resultado = DB::table('instituicoes_instituicoes as ii')
            ->selectRaw(
                "COUNT(CASE
                    WHEN mr.dt_recepcao <= ? AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > ?) THEN mm.id
                    ELSE NULL
                END) AS total_ate_datafinal",
                [$dataFinal->toDateString(), $dataFinal->toDateString()]
            )
            ->leftJoin('membresia_membros as mm', function ($join) {
                $join->on('ii.id', '=', 'mm.igreja_id')
                    ->where('mm.vinculo', 'M')
                    ->where('mm.status', 'A');
            })
            ->leftJoin('membresia_rolpermanente as mr', function ($join) {
                $join->on('mr.membro_id', '=', 'mm.id');
            })
            ->whereIn('ii.instituicao_pai_id', $distritoIds)
            ->where('ii.ativo', 1)
            ->first();

        return (int) ($resultado->total_ate_datafinal ?? 0);
    }

    private function totalCongregadosRelatorio(int $regiaoId): int
    {
        return (int) DB::table('membresia_membros as mm')
            ->leftJoin('membresia_contatos as mc', function ($join) {
                $join->on('mc.membro_id', '=', 'mm.id')
                    ->whereNull('mc.deleted_at');
            })
            ->leftJoin('congregacoes_congregacoes as cc', 'cc.id', '=', 'mm.congregacao_id')
            ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'mm.igreja_id')
            ->leftJoin('instituicoes_instituicoes as dist', 'dist.id', '=', 'igreja.instituicao_pai_id')
            ->whereNull('mm.deleted_at')
            ->where('mm.vinculo', 'C')
            ->where('mm.status', 'A')
            ->where('dist.instituicao_pai_id', $regiaoId)
            ->count('mm.id');
    }

    private function totalClerigos(int $regiaoId): int
    {
        return (int) DB::table('pessoas_pessoas as pp')
            ->join('pessoas_nomeacoes as pn', function ($join) {
                $join->on('pp.id', '=', 'pn.pessoa_id');
            })
            ->join('instituicoes_instituicoes as ii', function ($join) {
                $join->on('pn.instituicao_id', '=', 'ii.id')
                    ->where('ii.ativo', '=', 1);
            })
            ->where([
                'pp.status_id' => 1,
                'ii.ativo' => 1,
                'pp.regiao_id' => $regiaoId,
            ])
            ->whereNull('pn.data_termino')
            ->distinct('pp.id')
            ->count('pp.id');
    }

    private function totalCongregacoes(int $regiaoId): int
    {
        return (int) DB::table('instituicoes_instituicoes as ii')
            ->join('congregacoes_congregacoes as cc', 'ii.id', '=', 'cc.instituicao_id')
            ->join('instituicoes_instituicoes as ip', 'ip.id', '=', 'ii.instituicao_pai_id')
            ->where('ii.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('ii.regiao_id', $regiaoId)
            ->where('ii.ativo', 1)
            ->whereNull('ii.data_encerramento')
            ->where('ip.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('ip.ativo', 1)
            ->whereNull('ip.data_encerramento')
            ->where('cc.ativo', 1)
            ->count();
    }

    private function totalArrecadacao(int $regiaoId, array $distritoIds, array $igrejaIds, Carbon $inicioAno, Carbon $hoje): float
    {
        $instituicaoIds = array_values(array_unique(array_merge([$regiaoId], $distritoIds, $igrejaIds)));

        return (float) DB::table('financeiro_lancamentos')
            ->whereNull('deleted_at')
            ->where('tipo_lancamento', 'E')
            ->where(function ($query) {
                $query->where('estornado', 0)->orWhereNull('estornado');
            })
            ->whereBetween('data_movimento', [$inicioAno->toDateString(), $hoje->toDateString()])
            ->where(function ($query) use ($regiaoId, $instituicaoIds) {
                $query->where('hist_regiao_id', $regiaoId);

                if (!empty($instituicaoIds)) {
                    $query->orWhereIn('instituicao_id', $instituicaoIds);
                }
            })
            ->sum('valor');
    }

    private function totalGceus(int $regiaoId): int
    {
        $subGceus = DB::table('gceu_cadastros as gc')
            ->select('gc.instituicao_id as igreja_id', DB::raw('COUNT(DISTINCT gc.id) as qtd_gceus'))
            ->where('gc.status', 'A')
            ->groupBy('gc.instituicao_id');

        return (int) DB::table('instituicoes_instituicoes as distrito')
            ->join('instituicoes_instituicoes as igreja', function ($join) {
                $join->on('igreja.instituicao_pai_id', '=', 'distrito.id')
                    ->where('igreja.tipo_instituicao_id', 1);
            })
            ->leftJoinSub($subGceus, 'sg', function ($join) {
                $join->on('sg.igreja_id', '=', 'igreja.id');
            })
            ->where('distrito.instituicao_pai_id', $regiaoId)
            ->where('distrito.tipo_instituicao_id', 2)
            ->sum(DB::raw('COALESCE(sg.qtd_gceus, 0)'));
    }

    private function totalIntegrantesGceus(array $igrejaIds): int
    {
        if (empty($igrejaIds)) {
            return 0;
        }

        return (int) DB::table('gceu_membros as gm')
            ->join('gceu_cadastros as gc', 'gc.id', '=', 'gm.gceu_cadastro_id')
            ->whereIn('gc.instituicao_id', $igrejaIds)
            ->where('gc.status', 'A')
            ->whereNull('gc.deleted_at')
            ->whereNull('gm.deleted_at')
            ->distinct('gm.membro_id')
            ->count('gm.membro_id');
    }

    private function totalMinisterios(): int
    {
        return (int) DB::table('membresia_setores')
            ->whereNull('deleted_at')
            ->count();
    }

    private function totalIntegrantesMinisterios(int $regiaoId): int
    {
        return (int) DB::table('membresia_funcoesministeriais as mfm')
            ->join('membresia_membros as mm', 'mm.id', '=', 'mfm.membro_id')
            ->where('mm.regiao_id', $regiaoId)
            ->where('mm.status', 'A')
            ->where('mm.vinculo', 'M')
            ->whereNull('mm.deleted_at')
            ->whereNull('mfm.deleted_at')
            ->whereNull('mfm.data_saida')
            ->distinct('mfm.membro_id')
            ->count('mfm.membro_id');
    }

    private function totalRolPermanente(int $regiaoId, string $status, string $campoData, Carbon $inicioAno, Carbon $hoje): int
    {
        return (int) DB::table('membresia_rolpermanente')
            ->where('regiao_id', $regiaoId)
            ->where('status', $status)
            ->whereNull('deleted_at')
            ->whereBetween($campoData, [$inicioAno->toDateString(), $hoje->toDateString()])
            ->count();
    }
}
