<?php

namespace App\Services\ServiceRelatorio;

use App\Models\InstituicoesInstituicao;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class IdentificaDadosRelatorioCongregadosService
{
    use Identifiable;

    public function executeLocal(): array
    {
        $igreja = Identifiable::fetchSessionIgrejaLocal();

        return $this->buildReportData('local', $igreja->id, $igreja->nome, 'Relatórios');
    }

    public function executeDistrito(): array
    {
        $distritoId = session()->get('session_perfil')->instituicao_id;
        $distrito = InstituicoesInstituicao::find($distritoId);

        return $this->buildReportData('distrito', $distritoId, optional($distrito)->nome, 'Relatórios Distritais');
    }

    public function executeRegiao(): array
    {
        $regiao = Identifiable::fetchtSessionRegiao();

        return $this->buildReportData('regiao', $regiao->id, $regiao->nome, 'Relatórios Regionais');
    }

    private function buildReportData(string $nivel, int $instituicaoId, ?string $instituicaoNome, string $breadcrumbGrupo): array
    {
        $congregados = $this->fetchCongregadosAtivos($nivel, $instituicaoId);
        $totaisPorIgreja = $congregados
            ->groupBy('igreja_id')
            ->map(function ($itens) {
                $primeiro = $itens->first();

                return (object) [
                    'distrito_nome' => $primeiro->distrito_nome,
                    'igreja_nome' => $primeiro->igreja_nome,
                    'total' => $itens->count(),
                ];
            })
            ->sortBy([
                ['distrito_nome', 'asc'],
                ['igreja_nome', 'asc'],
            ])
            ->values();

        return [
            'nivel' => $nivel,
            'titulo' => 'RELATÓRIO DE CONGREGADOS - ' . $instituicaoNome,
            'instituicaoNome' => $instituicaoNome,
            'breadcrumbGrupo' => $breadcrumbGrupo,
            'dataSolicitacao' => now(),
            'congregados' => $congregados,
            'totaisPorIgreja' => $totaisPorIgreja,
            'totalGeral' => $congregados->count(),
        ];
    }

    private function fetchCongregadosAtivos(string $nivel, int $instituicaoId)
    {
        return DB::table('membresia_membros as mm')
            ->leftJoin('membresia_contatos as mc', function ($join) {
                $join->on('mc.membro_id', '=', 'mm.id')
                    ->whereNull('mc.deleted_at');
            })
            ->leftJoin('congregacoes_congregacoes as cc', 'cc.id', '=', 'mm.congregacao_id')
            ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'mm.igreja_id')
            ->leftJoin('instituicoes_instituicoes as dist', 'dist.id', '=', 'igreja.instituicao_pai_id')
            ->select(
                'mm.id',
                'mm.nome as congregado_nome',
                'mm.created_at as data_cadastro',
                'mm.igreja_id',
                'dist.nome as distrito_nome',
                'igreja.nome as igreja_nome',
                DB::raw("CASE WHEN mm.congregacao_id IS NULL OR mm.congregacao_id = 0 THEN 'Sede' ELSE COALESCE(cc.nome, 'Congregação sem nome') END as localidade_nome"),
                'mc.bairro',
                DB::raw("CASE WHEN mc.telefone_preferencial IS NOT NULL AND mc.telefone_preferencial <> '' THEN mc.telefone_preferencial
                    WHEN mc.telefone_alternativo IS NOT NULL AND mc.telefone_alternativo <> '' THEN mc.telefone_alternativo
                    ELSE mc.telefone_whatsapp END as contato")
            )
            ->whereNull('mm.deleted_at')
            ->where('mm.vinculo', MembresiaMembro::VINCULO_CONGREGADO)
            ->where('mm.status', MembresiaMembro::STATUS_ATIVO)
            ->when($nivel === 'local', fn ($query) => $query->where('mm.igreja_id', $instituicaoId))
            ->when($nivel === 'distrito', fn ($query) => $query->where('igreja.instituicao_pai_id', $instituicaoId))
            ->when($nivel === 'regiao', fn ($query) => $query->where('dist.instituicao_pai_id', $instituicaoId))
            ->orderBy('dist.nome')
            ->orderBy('igreja.nome')
            ->orderBy('localidade_nome')
            ->orderBy('mm.nome')
            ->get();
    }
}
