<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\ReintegrarMembroException;
use App\Models\MembresiaMembro;
use App\Models\MembresiaSituacao;
use App\Traits\Identifiable;

class IdentificaDadosReintegrarMembroService
{ 
    use Identifiable;

    public function execute($membroId)
    {
        $pessoa = MembresiaMembro::withTrashed()
            ->where('id', $membroId)
            ->where('vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->where(function ($query) {
                $query->where('status', MembresiaMembro::STATUS_INATIVO)
                    ->orWhereNotNull('deleted_at');
            })
            ->first();

        if (!$pessoa) {
            throw new ReintegrarMembroException();
        }

        return [
            'pessoa'       => $pessoa,
            'sugestao_rol' => Identifiable::fetchSugestaoRol($membroId),
            'pastores'     => Identifiable::fetchPastores(),
            'modos'        => Identifiable::fetchModos(MembresiaSituacao::TIPO_ADESAO),
            'congregacoes' => Identifiable::fetchCongregacoes()
        ];
    }
}
