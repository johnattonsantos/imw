<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MapaoRegionalService
{
    public function execute(?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $regiao = Identifiable::fetchtSessionRegiao();
        $regiaoId = (int) $regiao->id;

        $distritoIds = $this->distritoIds($regiaoId);
        $igrejaIds = $this->igrejaIds($distritoIds);

        $dataInicialPeriodo = $dataInicial
            ? Carbon::parse($dataInicial)->startOfDay()
            : Carbon::now()->startOfYear()->startOfDay();
        $dataFinalPeriodo = $dataFinal
            ? Carbon::parse($dataFinal)->endOfDay()
            : Carbon::now()->endOfDay();
        $mesesPeriodo = $this->mesesNoPeriodo($dataInicialPeriodo, $dataFinalPeriodo);
        $trimestresPeriodo = $this->trimestresNoPeriodo($dataInicialPeriodo, $dataFinalPeriodo);

        $totalArrecadacao = $this->totalArrecadacao($regiaoId, $distritoIds, $igrejaIds, $dataInicialPeriodo, $dataFinalPeriodo);
        $totalRecebimentos = $this->totalRolPermanente($regiaoId, 'A', 'dt_recepcao', $dataInicialPeriodo, $dataFinalPeriodo);
        $totalExclusoes = $this->totalRolPermanente($regiaoId, 'I', 'dt_exclusao', $dataInicialPeriodo, $dataFinalPeriodo);

        return [
            'regiao' => $regiao,
            'cards' => [
                ['titulo' => 'Total de membros', 'valor' => $this->totalMembresia($regiaoId, 'M'), 'tipo' => 'numero'],
                ['titulo' => 'Total de clérigos', 'valor' => $this->totalClerigos($regiaoId), 'tipo' => 'numero'],
                ['titulo' => 'Total de congregados', 'valor' => $this->totalMembresia($regiaoId, 'C'), 'tipo' => 'numero'],
                ['titulo' => 'Total de distritos', 'valor' => count($distritoIds), 'tipo' => 'numero'],
                ['titulo' => 'Total de igrejas', 'valor' => count($igrejaIds), 'tipo' => 'numero'],
                ['titulo' => 'Total de congregações', 'valor' => $this->totalCongregacoes($igrejaIds), 'tipo' => 'numero'],
                ['titulo' => 'Média da arrecadação mensal', 'valor' => $totalArrecadacao / $mesesPeriodo, 'tipo' => 'moeda'],
                ['titulo' => 'Total de GCEUs', 'valor' => $this->totalGceus($igrejaIds), 'tipo' => 'numero'],
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
            ],
        ];
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
        return DB::table('instituicoes_instituicoes')
            ->where('instituicao_pai_id', $regiaoId)
            ->where('ativo', 1)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function igrejaIds(array $distritoIds): array
    {
        if (empty($distritoIds)) {
            return [];
        }

        return DB::table('instituicoes_instituicoes')
            ->whereIn('instituicao_pai_id', $distritoIds)
            ->where('tipo_instituicao_id', 1)
            ->where('ativo', 1)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function totalMembresia(int $regiaoId, string $vinculo): int
    {
        return (int) DB::table('membresia_membros')
            ->where('regiao_id', $regiaoId)
            ->where('status', 'A')
            ->where('vinculo', $vinculo)
            ->whereNull('deleted_at')
            ->count();
    }

    private function totalClerigos(int $regiaoId): int
    {
        $query = DB::table('pessoas_pessoas')
            ->where('regiao_id', $regiaoId)
            ->where('situacao_id', 1)
            ->whereRaw('LOWER(categoria) IN (?, ?, ?, ?)', ['ministro', 'pastor', 'missionária', 'missionaria']);

        if (Schema::hasColumn('pessoas_pessoas', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return (int) $query->count();
    }

    private function totalCongregacoes(array $igrejaIds): int
    {
        if (empty($igrejaIds)) {
            return 0;
        }

        return (int) DB::table('congregacoes_congregacoes')
            ->whereIn('instituicao_id', $igrejaIds)
            ->where('ativo', 1)
            ->whereNull('deleted_at')
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

    private function totalGceus(array $igrejaIds): int
    {
        if (empty($igrejaIds)) {
            return 0;
        }

        return (int) DB::table('gceu_cadastros')
            ->whereIn('instituicao_id', $igrejaIds)
            ->where('status', 'A')
            ->whereNull('deleted_at')
            ->count();
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
