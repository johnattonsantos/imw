<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\StoreRolPermanenteException;
use App\Models\GCeuMembros;
use App\Models\MembresiaFuncaoMinisterial;
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
            $reintegracaoOutraIgreja = (int) $pessoa->igreja_id !== (int) $params['igreja_id'];
            
            $pessoa->restore();
            $dadosMembro = [
                'status'         => MembresiaMembro::STATUS_ATIVO,
                'vinculo'        => MembresiaMembro::VINCULO_MEMBRO,
                'rol_atual'      => $params['numero_rol'],
                'regiao_id'      => $params['regiao_id'],
                'distrito_id'    => $params['distrito_id'],
                'igreja_id'      => $params['igreja_id'],
                'congregacao_id' => $params['congregacao_id'],
            ];

            if ($reintegracaoOutraIgreja) {
                $dadosMembro['funcao_eclesiastica_id'] = null;
                $dadosMembro['gceu_id'] = null;
            }

            $pessoa->update($dadosMembro);

            if ($reintegracaoOutraIgreja) {
                MembresiaFuncaoMinisterial::where('membro_id', $pessoa->id)->delete();
                GCeuMembros::where('membro_id', $pessoa->id)->delete();
            }

            MembresiaRolPermanente::withTrashed()
                ->where('membro_id', $pessoa->id)
                ->where('lastrec', 1)
                ->update(['lastrec' => 0]);

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
        $params['lastrec']     = 1;

        return [...$params, ...Identifiable::fetchSessionInstituicoesStoreMembresia()];
    }
}
