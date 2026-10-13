<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\DeleteMembroException;
use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Traits\Identifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DeletarMembroService
{
    use Identifiable;

    public function execute($data, $id): void
    {
        $data['dt_exclusao'] = Carbon::today()->toDateString();
        try {
            DB::beginTransaction();

            $membro   = $this->fetchMembro($id);
            $rolAtual = Identifiable::fetchRolAtual($membro->id, null, $membro->igreja_id);

            if (!$rolAtual->dt_recepcao) {
                throw ValidationException::withMessages([
                    'dt_exclusao' => 'Não foi possível confirmar a data de recepção nesta igreja. Confira o rol do membro.',
                ]);
            }

            Validator::make($data, [
                'dt_exclusao' => ['required', 'date', 'after_or_equal:'.$rolAtual->dt_recepcao->toDateString()],
            ], [
                'dt_exclusao.after_or_equal' => 'A data de exclusão não pode ser anterior à data de recepção nesta igreja ('.$rolAtual->dt_recepcao->format('d/m/Y').').',
            ])->validate();

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
            if ($e instanceof ValidationException) {
                throw $e;
            }
            throw new DeleteMembroException('', 0, $e);
        }
    }

    private function fetchMembro($id)
    {
        return MembresiaMembro::findOrFail($id);
    }
}
