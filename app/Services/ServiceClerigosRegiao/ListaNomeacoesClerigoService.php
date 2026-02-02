<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\InstituicoesInstituicao;
use App\Models\PessoaNomeacao;
use App\Models\PessoasPessoa;
use App\Traits\Identifiable;

class ListaNomeacoesClerigoService
{
    public function execute($clerigoId, $status = null): array
    {
        $nomeacoes = PessoaNomeacao::withTrashed(['funcaoMinisterial', 'instituicao.instituicaoPai'])
            ->where('pessoa_id', $clerigoId)
            ->when($status == 'ativo', fn($query) => $query->whereNull('data_termino'))
            ->when($status == 'inativo', fn($query) => $query->whereNotNull('data_termino'))
            ->orderByRaw('data_termino IS NULL DESC')
            ->orderBy('data_nomeacao', 'desc')
            ->orderBy(
                InstituicoesInstituicao::select('nome')
                    ->whereColumn('instituicoes_instituicoes.id', 'pessoas_nomeacoes.instituicao_id')
                    ->limit(1),
                'asc'
            )
            ->get();

        return [
            'nomeacoes' => $nomeacoes,
            'status'    => $status,
            'clerigoId'        => $clerigoId
        ];
    }

    public function instituicao($id): array
    { 
        $instituicao = Identifiable::fetchInstituicao($id);
        $nomeacoes = PessoaNomeacao::where('instituicao_id', $id)
            ->join('pessoas_pessoas', 'pessoas_pessoas.id', 'pessoas_nomeacoes.pessoa_id')
            ->with('funcaoministerial')
            ->with('pessoa')
            ->with('instituicao')
            ->orderByRaw('data_termino IS NULL DESC')
            //->orderBy('pessoas_nomeacoes.data_nomeacao', 'DESC')
            ->orderBy('pessoas_pessoas.nome', 'ASC')
            ->get();
        return [
            'nomeacoes' => $nomeacoes,
            'instituicao'  => $instituicao,
        ];
    }
}