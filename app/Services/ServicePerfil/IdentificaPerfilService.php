<?php 

namespace App\Services\ServicePerfil;

use App\Dtos\SessionInstituicoesDto;
use App\Exceptions\TipoInstituicaoNotFoundException;
use App\Models\InstituicoesInstituicao;

class IdentificaPerfilService
{
    public function execute($instituicaoId, $instituicaoNome, $perfilId, $perfilNome)
    {
        $instituicaoNome = $instituicaoNome ?: 'Acesso Global';

        return (object) [
            'instituicao_id'   => $instituicaoId,
            'instituicao_nome' => $instituicaoNome,
            'perfil_id'        => $perfilId,
            'perfil_nome'      => $perfilNome,
            'instituicoes'     => $instituicaoId
                ? $this->fetchSessionIstituicoes(new SessionInstituicoesDto(), $instituicaoId)
                : new SessionInstituicoesDto()
        ];
    }

    private function fetchSessionIstituicoes(SessionInstituicoesDto $dto, $instituicaoId)
    {
        $instituicao = InstituicoesInstituicao::findOr($instituicaoId, fn() => throw new TipoInstituicaoNotFoundException());
        $tipoInstituicao = $dto->getField($instituicao->tipo_instituicao_id);
        $dto->{$tipoInstituicao} = $instituicao;
        
        if($instituicao->instituicao_pai_id) 
            return $this->fetchSessionIstituicoes($dto, $instituicao->instituicao_pai_id);

        return $dto;
    }
}
