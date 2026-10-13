<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\DeleteMembroException;
use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class DeletarMembroService
{
    use Identifiable;

    public function execute($data, $id): void
    {
        try {
            DB::beginTransaction();

            $membro   = $this->fetchMembro($id);
            $rolAtual = Identifiable::fetchRolAtual($membro->id, null, $membro->igreja_id);

            $rolAtual->update([
                'modo_exclusao_id' => $data['modo_exclusao_id'], 
                'dt_exclusao'      => $data['dt_exclusao'], 
                'status'           => MembresiaRolPermanente::STATUS_EXCLUSAO
            ]);

            $membro->update(['status' => MembresiaMembro::STATUS_INATIVO]);
            //$membro->delete();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new DeleteMembroException('', 0, $e);
        }
    }

    private function fetchMembro($id)
    {
        return MembresiaMembro::findOrFail($id);
    }
}
