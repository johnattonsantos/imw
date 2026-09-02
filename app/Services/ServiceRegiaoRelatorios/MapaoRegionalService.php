<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\InstituicoesTipoInstituicao;
use App\Support\PeriodoEclesiastico;
use App\Traits\Identifiable;
use App\Traits\MembrosMinisterioUtils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MapaoRegionalService
{
    use MembrosMinisterioUtils;

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
        $detalhesIntegrantesGceus = $this->detalhesIntegrantesGceus($igrejaIds);
        $detalhesMinisterios = $this->detalhesMinisterios($regiaoId, $dataInicialPeriodo, $dataFinalPeriodo);
        $detalhesRecebimentos = $this->detalhesRolPermanentePorIgreja($regiaoId, 'A', 'dt_recepcao', $dataInicialPeriodo, $dataFinalPeriodo);
        $detalhesExclusoes = $this->detalhesRolPermanentePorIgreja($regiaoId, 'I', 'dt_exclusao', $dataInicialPeriodo, $dataFinalPeriodo);
        $totalRecebimentos = (int) $detalhesRecebimentos->sum('total');
        $totalExclusoes = (int) $detalhesExclusoes->sum('total');
        $totalMembrosInicioPeriodo = $this->totalMembrosNaData($regiaoId, $dataInicialPeriodo->copy()->subDay());

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
                ['titulo' => 'Total de integrantes de GCEUs', 'valor' => $this->totalIntegrantesGceus($detalhesIntegrantesGceus), 'tipo' => 'numero'],
                ['titulo' => 'Total de ministérios', 'valor' => $this->totalMinisterios(), 'tipo' => 'numero'],
                ['titulo' => 'Total de integrantes nos ministérios', 'valor' => $this->totalIntegrantesMinisterios($regiaoId), 'tipo' => 'numero'],
                [
                    'titulo' => 'Média de recebimento de membros no trimestre',
                    'valor' => $totalRecebimentos / $trimestresPeriodo,
                    'tipo' => 'decimal',
                    'detalhe' => 'recebimentos',
                    'resumo' => [
                        ['titulo' => 'Total que tinha', 'valor' => $totalMembrosInicioPeriodo],
                        ['titulo' => 'Total que entrou', 'valor' => $totalRecebimentos],
                    ],
                ],
                [
                    'titulo' => 'Média de exclusões por trimestre',
                    'valor' => $totalExclusoes / $trimestresPeriodo,
                    'tipo' => 'decimal',
                    'detalhe' => 'exclusoes',
                    'resumo' => [
                        ['titulo' => 'Total que tinha', 'valor' => $totalMembrosInicioPeriodo],
                        ['titulo' => 'Total que saiu', 'valor' => $totalExclusoes],
                    ],
                ],
            ],
            'periodos' => [
                'data_inicial' => $dataInicialPeriodo,
                'data_final' => $dataFinalPeriodo,
                'meses_periodo' => $mesesPeriodo,
                'trimestres_periodo' => $trimestresPeriodo,
                'descricao' => 'Biênio corrente',
            ],
            'detalhesIntegrantesGceus' => $detalhesIntegrantesGceus,
            'detalhesMinisterios' => $detalhesMinisterios,
            'detalhesRecebimentos' => $detalhesRecebimentos,
            'detalhesExclusoes' => $detalhesExclusoes,
            'tiposMinisterios' => $this->tiposMinisterios(),
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
        $distritoIds = DB::table('instituicoes_instituicoes')
            ->where('instituicao_pai_id', $regiaoId)
            ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->whereNull('data_encerramento')
            ->pluck('id')
            ->toArray();

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

    private function totalIntegrantesGceus($detalhesIntegrantesGceus): int
    {
        return (int) $detalhesIntegrantesGceus->sum('total_integrantes');
    }

    private function detalhesIntegrantesGceus(array $igrejaIds)
    {
        if (empty($igrejaIds)) {
            return collect();
        }

        return DB::table('gceu_cadastros as gc')
            ->leftJoin('gceu_membros as gm', function ($join) {
                $join->on('gm.gceu_cadastro_id', '=', 'gc.id')
                    ->whereNull('gm.deleted_at');
            })
            ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'gc.instituicao_id')
            ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->select(
                'gc.id',
                'gc.nome',
                'igreja.nome as igreja_nome',
                'distrito.nome as distrito_nome',
                DB::raw('COUNT(DISTINCT gm.membro_id) as total_integrantes')
            )
            ->whereIn('gc.instituicao_id', $igrejaIds)
            ->where('gc.status', 'A')
            ->whereNull('gc.deleted_at')
            ->groupBy('gc.id', 'gc.nome', 'igreja.nome', 'distrito.nome')
            ->orderBy('gc.nome')
            ->get();
    }

    private function totalMinisterios(): int
    {
        return count($this->tiposMinisterios());
    }

    private function detalhesMinisterios(int $regiaoId, Carbon $dataInicial, Carbon $dataFinal)
    {
        $rows = self::fetch(
            $dataInicial->toDateString(),
            $dataFinal->toDateString(),
            'M',
            'all',
            $regiaoId
        );

        return $rows->flatMap(function ($row) {
            return collect($this->tiposMinisterios())->map(function ($tipo) use ($row) {
                return [
                    'distrito_id' => $row->distrito_id ?? null,
                    'distrito_nome' => $row->distrito,
                    'igreja_id' => $row->igreja_id ?? null,
                    'igreja_nome' => $row->nome,
                    'tipo' => $tipo['id'],
                    'nome' => $tipo['nome'],
                    'total' => (int) ($row->{$tipo['campo']} ?? 0),
                ];
            });
        })->values();
    }

    private function tiposMinisterios(): array
    {
        return [
            ['id' => 'kids', 'nome' => 'KIDS', 'campo' => 'Kids_Y'],
            ['id' => 'conexao', 'nome' => 'CONEXÃO', 'campo' => 'Conexao_Y'],
            ['id' => 'fire', 'nome' => 'FIRE', 'campo' => 'Fire_Y'],
            ['id' => 'move', 'nome' => 'MOVE', 'campo' => 'Move_Y'],
            ['id' => 'homens', 'nome' => 'HOMENS', 'campo' => 'Homens_Y'],
            ['id' => 'mulheres', 'nome' => 'MULHERES', 'campo' => 'Mulheres_Y'],
            ['id' => 'sessenta', 'nome' => '60+', 'campo' => 'Sessenta_Y'],
        ];
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

    private function totalMembrosNaData(int $regiaoId, Carbon $data): int
    {
        return (int) DB::table('membresia_rolpermanente')
            ->where('regiao_id', $regiaoId)
            ->whereNull('deleted_at')
            ->where('dt_recepcao', '<=', $data->toDateString())
            ->where(function ($query) use ($data) {
                $query->whereNull('dt_exclusao')
                    ->orWhere('dt_exclusao', '>', $data->toDateString());
            })
            ->count();
    }

    private function detalhesRolPermanentePorIgreja(
        int $regiaoId,
        string $status,
        string $campoData,
        Carbon $dataInicial,
        Carbon $dataFinal
    ) {
        return DB::table('membresia_rolpermanente as mr')
            ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'mr.igreja_id')
            ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'mr.distrito_id')
            ->select(
                'mr.igreja_id',
                'igreja.nome as igreja_nome',
                'mr.distrito_id',
                'distrito.nome as distrito_nome',
                DB::raw('COUNT(mr.id) as total')
            )
            ->where('mr.regiao_id', $regiaoId)
            ->where('mr.status', $status)
            ->whereNull('mr.deleted_at')
            ->whereBetween("mr.$campoData", [$dataInicial->toDateString(), $dataFinal->toDateString()])
            ->groupBy('mr.igreja_id', 'igreja.nome', 'mr.distrito_id', 'distrito.nome')
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->get();
    }
}
