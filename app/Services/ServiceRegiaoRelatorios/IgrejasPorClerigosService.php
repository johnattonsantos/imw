<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\InstituicoesTipoInstituicao;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class IgrejasPorClerigosService
{
    public function execute(array $params = []): array
    {
        $regiao = Identifiable::fetchtSessionRegiao();

        $distritoId = $params['distrito'] ?? 'all';
        $igrejaId = $params['igreja'] ?? 'all';
        $clerigoId = $params['clerigo'] ?? 'all';

        return [
            'regiao' => $regiao,
            'distritos' => Identifiable::fetchDistritosByRegiao($regiao->id),
            'igrejas' => $this->fetchIgrejas($regiao->id),
            'clerigos' => $this->fetchClerigos($regiao->id),
            'lancamentos' => $this->fetchLancamentos($regiao->id, $distritoId, $igrejaId, $clerigoId),
        ];
    }

    private function fetchIgrejas(int $regiaoId)
    {
        return DB::table('instituicoes_instituicoes as igreja')
            ->join('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->select('igreja.id', 'igreja.nome')
            ->where('igreja.regiao_id', $regiaoId)
            ->where('igreja.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('igreja.ativo', 1)
            ->whereNull('igreja.deleted_at')
            ->where('distrito.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->whereNull('distrito.deleted_at')
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->get();
    }

    private function fetchClerigos(int $regiaoId)
    {
        return DB::table('pessoas_pessoas as pp')
            ->select('pp.id', 'pp.nome')
            ->join('pessoas_nomeacoes as pn', 'pn.pessoa_id', '=', 'pp.id')
            ->join('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'pn.instituicao_id')
            ->where('igreja.regiao_id', $regiaoId)
            ->where('igreja.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->whereNull('pp.deleted_at')
            ->whereNull('pn.deleted_at')
            ->whereNull('igreja.deleted_at')
            ->distinct()
            ->orderBy('pp.nome')
            ->get();
    }

    private function fetchLancamentos(int $regiaoId, string $distritoId, string $igrejaId, string $clerigoId)
    {
        $query = DB::table('pessoas_nomeacoes as pn')
            ->join('pessoas_pessoas as pp', 'pp.id', '=', 'pn.pessoa_id')
            ->join('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'pn.instituicao_id')
            ->join('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->select(
                'distrito.nome as distrito_nome',
                'igreja.nome as igreja_nome',
                'pp.nome as clerigo_nome',
                'pn.data_nomeacao as data_inicio_nomeacao',
                'pn.data_termino as data_fim_nomeacao',
                DB::raw("(
                    SELECT COUNT(DISTINCT mr.membro_id)
                    FROM membresia_rolpermanente mr
                    WHERE mr.igreja_id = igreja.id
                        AND mr.dt_recepcao <= pn.data_nomeacao
                        AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > pn.data_nomeacao)
                        AND mr.deleted_at IS NULL
                ) as total_membros_inicio"),
                DB::raw("(
                    SELECT COUNT(DISTINCT mr.membro_id)
                    FROM membresia_rolpermanente mr
                    WHERE mr.igreja_id = igreja.id
                        AND mr.dt_recepcao <= COALESCE(pn.data_termino, CURDATE())
                        AND (mr.dt_exclusao IS NULL OR mr.dt_exclusao > COALESCE(pn.data_termino, CURDATE()))
                        AND mr.deleted_at IS NULL
                ) as total_membros_fim")
            )
            ->where('igreja.regiao_id', $regiaoId)
            ->where('igreja.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('distrito.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
            ->where('igreja.ativo', 1)
            ->where('distrito.ativo', 1)
            ->whereNull('pn.deleted_at')
            ->whereNull('pp.deleted_at')
            ->whereNull('igreja.deleted_at')
            ->whereNull('distrito.deleted_at')
            ->when($distritoId !== 'all' && is_numeric($distritoId), function ($q) use ($distritoId) {
                $q->where('distrito.id', (int) $distritoId);
            })
            ->when($igrejaId !== 'all' && is_numeric($igrejaId), function ($q) use ($igrejaId) {
                $q->where('igreja.id', (int) $igrejaId);
            })
            ->when($clerigoId !== 'all' && is_numeric($clerigoId), function ($q) use ($clerigoId) {
                $q->where('pp.id', (int) $clerigoId);
            })
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->orderByDesc('pn.data_nomeacao');

        if (!isset(request()->action)) {
            return collect();
        }

        return $query->get();
    }
}
