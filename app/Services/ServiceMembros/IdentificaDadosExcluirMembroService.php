<?php 

namespace App\Services\ServiceMembros;

use App\Exceptions\IdentificaDadosExcluirMembroException;
use App\Models\MembresiaMembro;
use App\Models\MembresiaSituacao;
use App\Traits\Identifiable;

class IdentificaDadosExcluirMembroService
{
    use Identifiable;

    public function execute($id)
    {
        $membro = Identifiable::fetchPessoa($id, MembresiaMembro::VINCULO_MEMBRO);

        $rolAtual = Identifiable::fetchRolAtual($membro->id, null, $membro->igreja_id);
        if($rolAtual->dt_exclusao) {
            throw new IdentificaDadosExcluirMembroException('Não pe possível excluir um membro já excluído');
        }
        return [
            'pessoa' => $membro,
            'modos' => Identifiable::fetchModos(MembresiaSituacao::TIPO_EXCLUSAO),
            'dataRecepcao' => optional($rolAtual->dt_recepcao)->toDateString(),
        ];
    }
}
