<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\StoreRolPermanenteException;
use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class StoreReintegracaoService
{
    use Identifiable;

    public function execute(array $params, $id)
    {
        try {
            $params = $this->fetchParams($params);

            DB::beginTransaction();
            $pessoa = MembresiaMembro::withTrashed()->findOrFail($id);
            
            $pessoa->restore();
            $pessoa->update([
                'status'         => MembresiaMembro::STATUS_ATIVO,
                'vinculo'        => MembresiaMembro::VINCULO_MEMBRO,
                'rol_atual'      => $params['numero_rol'], 
                'congregacao_id' => $params['congregacao_id'],
            ]);

            // atualiza o rol atual
            optional($pessoa->rolAtualSessionIgreja())->update(['lastrec' => 0]);

            $params['status'] = MembresiaRolPermanente::STATUS_RECEBIMENTO;
            $pessoa->rolPermanente()->create($params);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new StoreRolPermanenteException('Erro ao criar dados na tabela de Rol Permanente');
        }
    }
    
    private function fetchParams($params)
    {
        $params['status']      = MembresiaRolPermanente::STATUS_RECEBIMENTO;

        return [...$params, ...Identifiable::fetchSessionInstituicoesStoreMembresia()];
    }
}
