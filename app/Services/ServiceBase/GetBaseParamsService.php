<?php 

namespace App\Services\ServiceBase;

use App\Models\NotificacaoTransferencia;
use App\Traits\Identifiable;

class GetBaseParamsService
{
    public function execute()
    {
        return (object) [
            'notificacoesTransferencia' => $this->fetchNotificacoesTransferencia()
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
}