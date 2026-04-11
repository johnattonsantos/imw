<?php 

namespace App\Services\ServiceBase;

use App\Models\Comunicacao;
use App\Models\ComunicacaoLeituraIgreja;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\NotificacaoTransferencia;

class GetBaseParamsService
{
    public function execute()
    {
        $quantidadeNovasComunicacoes = $this->countNovasComunicacoes();

        return (object) [
            'notificacoesTransferencia' => $this->fetchNotificacoesTransferencia(),
            'hasNovaComunicacao' => $quantidadeNovasComunicacoes > 0,
            'quantidadeNovasComunicacoes' => $quantidadeNovasComunicacoes,
        ];
    }

    private function fetchNotificacoesTransferencia()
    {
        try {
            $sessionInstituicoes = session()->get('session_perfil')->instituicoes;
    
            return NotificacaoTransferencia::with([
                    'regiaoOrigem:id,nome',
                    'distritoOrigem:id,nome',
                    'igrejaOrigem:id,nome'
            ])
                ->where('igreja_destino_id', $sessionInstituicoes->igrejaLocal->id)
                ->whereNull('dt_aceite')
                ->whereNull('dt_rejeicao')
                ->get();
        } catch (\Exception $e) {
            return null;
        }

    }

    private function countNovasComunicacoes(): int
    {
        try {
            if (!auth()->check() || !session()->has('session_perfil')) {
                return 0;
            }

            $igrejaLocalId = $this->resolveIgrejaLocalId();
            $regiaoId = $this->resolveRegiaoId();

            if ($igrejaLocalId <= 0 || $regiaoId <= 0) {
                return 0;
            }

            $lidasIds = ComunicacaoLeituraIgreja::query()
                ->where('igreja_id', $igrejaLocalId)
                ->pluck('comunicacao_id');

            return Comunicacao::query()
                ->where('instituicao_id', $regiaoId)
                ->whereNotIn('id', $lidasIds)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function resolveRegiaoId(): int
    {
        $sessionPerfil = session('session_perfil');

        $regiaoId = (int) data_get($sessionPerfil, 'instituicoes.regiao.id', 0);
        if ($regiaoId > 0) {
            return $regiaoId;
        }

        $instituicaoId = (int) data_get($sessionPerfil, 'instituicao_id', 0);
        if ($instituicaoId <= 0) {
            return 0;
        }

        return $this->resolveRegiaoByInstituicaoId($instituicaoId);
    }

    private function resolveRegiaoByInstituicaoId(int $instituicaoId): int
    {
        $currentId = $instituicaoId;
        $maxDepth = 10;

        while ($currentId > 0 && $maxDepth-- > 0) {
            $instituicao = InstituicoesInstituicao::query()
                ->select(['id', 'tipo_instituicao_id', 'instituicao_pai_id', 'regiao_id'])
                ->find($currentId);

            if (!$instituicao) {
                return 0;
            }

            if ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::REGIAO) {
                return (int) $instituicao->id;
            }

            if (!empty($instituicao->regiao_id)) {
                return (int) $instituicao->regiao_id;
            }

            $currentId = (int) ($instituicao->instituicao_pai_id ?? 0);
        }

        return 0;
    }

    private function resolveIgrejaLocalId(): int
    {
        $sessionPerfil = session('session_perfil');

        $igrejaId = (int) data_get($sessionPerfil, 'instituicoes.igrejaLocal.id', 0);
        if ($igrejaId > 0) {
            return $igrejaId;
        }

        $instituicaoId = (int) data_get($sessionPerfil, 'instituicao_id', 0);
        if ($instituicaoId <= 0) {
            return 0;
        }

        $instituicao = InstituicoesInstituicao::query()
            ->select(['id', 'tipo_instituicao_id'])
            ->find($instituicaoId);

        if (!$instituicao) {
            return 0;
        }

        if ((int) $instituicao->tipo_instituicao_id === InstituicoesTipoInstituicao::IGREJA_LOCAL) {
            return (int) $instituicao->id;
        }

        return 0;
    }
}
