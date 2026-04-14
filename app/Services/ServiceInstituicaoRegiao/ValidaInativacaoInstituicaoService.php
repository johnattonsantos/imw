<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ValidaInativacaoInstituicaoService
{
    public function execute(InstituicoesInstituicao $instituicao): void
    {
        $motivos = [];

        $totalMembrosAtivos = $this->totalMembrosAtivos($instituicao);
        if ($totalMembrosAtivos > 0) {
            $motivos[] = 'Existem membros ativos vinculados (total: ' . $totalMembrosAtivos . ')';
        }

        $saldoTotal = $this->obterSaldoTotalCaixas($instituicao);
        if (round($saldoTotal, 2) != 0.0) {
            $motivos[] = 'Existe saldo em caixa (saldo atual: R$ ' . number_format($saldoTotal, 2, ',', '.') . ')';
        }

        $totalNomeacoesAtivas = $this->totalNomeacoesAtivas($instituicao);
        if ($totalNomeacoesAtivas > 0) {
            $motivos[] = 'Existem nomeações ativas vinculadas (total: ' . $totalNomeacoesAtivas . ')';
        }

        if (!empty($motivos)) {
            throw ValidationException::withMessages([
                'ativo' => "Não foi possível inativar a instituição. Motivos: " . implode(' | ', $motivos) . '.',
            ]);
        }
    }

    private function totalMembrosAtivos(InstituicoesInstituicao $instituicao): int
    {
        $coluna = $this->colunaRelacaoInstituicao($instituicao->tipo_instituicao_id);

        return DB::table('membresia_membros as mm')
            ->whereNull('mm.deleted_at')
            ->where('mm.status', 'A')
            ->where("mm.$coluna", $instituicao->id)
            ->count();
    }

    private function obterSaldoTotalCaixas(InstituicoesInstituicao $instituicao): float
    {
        $saldoTotal = DB::table('financeiro_caixas as fc')
            ->leftJoin('financeiro_lancamentos as fl', function ($join) {
                $join->on('fl.caixa_id', '=', 'fc.id')
                    ->whereNull('fl.deleted_at');
            })
            ->whereNull('fc.deleted_at')
            ->where('fc.instituicao_id', $instituicao->id)
            ->selectRaw("
                COALESCE(SUM(
                    CASE
                        WHEN fl.tipo_lancamento = 'E' THEN fl.valor
                        WHEN fl.tipo_lancamento = 'S' THEN -fl.valor
                        ELSE 0
                    END
                ), 0) AS saldo_total
            ")
            ->value('saldo_total');

        return (float) $saldoTotal;
    }

    private function totalNomeacoesAtivas(InstituicoesInstituicao $instituicao): int
    {
        return DB::table('pessoas_nomeacoes')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->whereNull('data_termino')
                    ->orWhereDate('data_termino', '>=', now()->toDateString());
            })
            ->where('instituicao_id', $instituicao->id)
            ->count();
    }

    private function colunaRelacaoInstituicao(int $tipoInstituicaoId): string
    {
        if ($tipoInstituicaoId === 1) {
            return 'igreja_id';
        }

        if ($tipoInstituicaoId === 2) {
            return 'distrito_id';
        }

        return 'regiao_id';
    }
}
