<?php

namespace App\Services\ServiceMembros;

use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;

class IdentificaDadosTransferenciaPorExclusaoService
{
    use Identifiable;

    public function execute($id)
    {
        return [
            'pessoa'   => $this->fetchPessoa($id, MembresiaMembro::VINCULO_MEMBRO),
            'igrejas'  => $this->fetchIgrejas()
        ];
    }

    private function fetchIgrejas()
    {
        // Subconsulta para obter IDs
        $subQuery = InstituicoesInstituicao::select('id');
 //           ->where('instituicao_pai_id', 23);

        // Consulta principal
        return InstituicoesInstituicao::query()
            ->leftJoin('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'instituicoes_instituicoes.instituicao_pai_id')
            ->whereIn('instituicoes_instituicoes.instituicao_pai_id', $subQuery)
            ->where('instituicoes_instituicoes.ativo', 1)
            ->where('instituicoes_instituicoes.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('instituicoes_instituicoes.id', '<>', Identifiable::fetchSessionIgrejaLocal()->id)
            ->select('instituicoes_instituicoes.*')
            ->with([
                'instituicaoPai:id,nome,instituicao_pai_id',
                'instituicaoPai.instituicaoPai:id,nome'
            ])
            ->orderBy('distrito.nome', 'asc')
            ->orderBy('instituicoes_instituicoes.nome', 'asc')
            ->get();
    }
}
