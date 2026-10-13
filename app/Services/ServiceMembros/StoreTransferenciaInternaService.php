<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\TransferenciaInternaException;
use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class StoreTransferenciaInternaService
{
    use Identifiable;

    public function execute($params, $id): void
    {
        try {
            DB::beginTransaction();

            $membro   = $this->fetchMembro($id);
            $rolAtual = Identifiable::fetchRolAtual($membro->id, null, $membro->igreja_id);

            $this->handleReCreateRol($membro->id, $rolAtual, $params);
            $membro->update(['congregacao_id' => $params['congregacao_id']]);
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new TransferenciaInternaException();
        }
    }

    private function fetchMembro($id)
    {
        return MembresiaMembro::findOrFail($id);
    }


    private function handleReCreateRol($membroId, $rolAtual, $params)
    {
        // exclui por transferencia interna no rol atual
        $rolAtual->update([
            'modo_exclusao_id' => 12, 
            'dt_exclusao'      => $params['dt_transferencia'],
            'status'           => MembresiaRolPermanente::STATUS_EXCLUSAO
        ]);

        // cria um novo rol com a congregacao atualizada
        MembresiaRolPermanente::create([
            'status'           => MembresiaRolPermanente::STATUS_RECEBIMENTO,
            'numero_rol'       => $rolAtual->numero_rol,
            'dt_recepcao'      => $params['dt_transferencia'],
            'clerigo_id'       => $params['clerigo_id'],
            'membro_id'        => $membroId,
            'modo_recepcao_id' => 12,
            'regiao_id'        => $rolAtual->regiao_id,
            'distrito_id'      => $rolAtual->distrito_id,
            'igreja_id'        => $rolAtual->igreja_id,
            'congregacao_id'   => $params['congregacao_id']
        ]);
    }
}
