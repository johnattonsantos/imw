<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\StoreNotificacaoExclusaoPorTransferenciaException;
use App\Exceptions\TransferenciaInternaException;
use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Models\NotificacaoTransferencia;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreNotificacaoExclusaoPorTransferenciaService
{
    use Identifiable;

    public function execute($params, $id): void
    {
        $params['dt_notificacao'] = now()->toDateString();
        try {
            DB::beginTransaction();

            $paramsNotificacao = $this->handleParamsCreateNotificacao($params, $id);
            NotificacaoTransferencia::create($paramsNotificacao);
                       
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new StoreNotificacaoExclusaoPorTransferenciaException();
        }
    }

    private function handleParamsCreateNotificacao($params, $membroId)
    {
        $instituicoesOrigem   = session('session_perfil')->instituicoes;
        $instituicoesDestino = $this->fetchInstituicoesDestino($params['igreja_id']);
        
        $params = [
            'membro_id'           => $membroId,
            'user_abertura'       => Auth::user()->id,
            'dt_abertura'         => $params['dt_notificacao'],

            // instituições de origem
            'regiao_origem_id'    => $instituicoesOrigem->regiao->id,
            'distrito_origem_id'  => $instituicoesOrigem->distrito->id,
            'igreja_origem_id'    => $instituicoesOrigem->igrejaLocal->id,

            // instituições de destino
            'regiao_destino_id'   => $instituicoesDestino->regiao->id,
            'distrito_destino_id' => $instituicoesDestino->distrito->id,
            'igreja_destino_id'   => $instituicoesDestino->igrejaLocal->id,
        ];

        return $params;
    }

    private function fetchInstituicoesDestino($igrejaId)
    {
        $igrejaLocal = InstituicoesInstituicao::find($igrejaId);
        $distrito    = InstituicoesInstituicao::where('id', $igrejaLocal->instituicao_pai_id)->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)->first();
        $regiao      = InstituicoesInstituicao::where('id', $distrito->instituicao_pai_id)->where('tipo_instituicao_id', InstituicoesTipoInstituicao::REGIAO)->first();

        return (object) [
            'igrejaLocal' => $igrejaLocal,
            'distrito'    => $distrito,
            'regiao'      => $regiao
        ];
    }
}
