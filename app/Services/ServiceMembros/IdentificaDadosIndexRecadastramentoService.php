<?php 

namespace App\Services\ServiceMembros;

use App\Models\MembresiaMembroRecadastramento;
use App\Traits\Identifiable;
use App\Traits\MemberCountableRecadastramento;

class IdentificaDadosIndexRecadastramentoService
{
    use MemberCountableRecadastramento, Identifiable;

    public function execute()
    {
        return [
            'countAtivos'      => MemberCountableRecadastramento::countAtivos(MembresiaMembroRecadastramento::VINCULO_MEMBRO),
            'countInativos' => MemberCountableRecadastramento::countInativos(MembresiaMembroRecadastramento::VINCULO_MEMBRO),
            'countTotal'  => MemberCountableRecadastramento::countTotal(MembresiaMembroRecadastramento::VINCULO_MEMBRO)
        ];
    }
}