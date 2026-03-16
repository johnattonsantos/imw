<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\IdentificaDadosExcluirMembroException;
use App\Models\MembresiaMembro;
use App\Models\MembresiaRolPermanente;
use App\Models\MembresiaSituacao;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\Auth;

class IdentificaDadosExcluirMembroService
{
    use Identifiable;

    public function execute($id)
    {
        return [
            'pessoa'       => $this->fetchMembroExcluir($id),
            'pastores'     => Identifiable::fetchPastores(),
            'modos'        => Identifiable::fetchModos(MembresiaSituacao::TIPO_EXCLUSAO),
        ];
    }

    private function fetchMembroExcluir($id)
    {
        $membro = Identifiable::fetchPessoa($id, MembresiaMembro::VINCULO_MEMBRO);

        $rolAtual = $membro->rolPermanente()
            ->orderByDesc('id')
            ->firstOr(fn() => throw new IdentificaDadosExcluirMembroException('Rol atual não identificado'));
        if($rolAtual->dt_exclusao) {
            throw new IdentificaDadosExcluirMembroException('Não pe possível excluir um membro já excluído');
        }
        return $membro;
    }
}