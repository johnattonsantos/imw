<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\InstituicoesInstituicao;
use App\Models\PessoaNomeacao;
use App\Models\PessoasPessoa;
use App\Traits\Identifiable;
use App\Traits\RegionalScope;
use Illuminate\Database\Eloquent\Collection;

class ListaNomeacoesClerigoService
{
    use RegionalScope;

    public function execute($clerigoId, $status = null): array
    {
        $regiaoId = $this->sessionRegiaoId();
        if (!$this->pessoaPertenceRegiao((int) $clerigoId, $regiaoId)) {
            return [
                'nomeacoes' => collect(),
                'status' => $status,
                'clerigoId' => $clerigoId,
            ];
        }

        $instituicoesPermitidas = $this->instituicoesPermitidas($regiaoId)->pluck('id')->toArray();

        $nomeacoes = PessoaNomeacao::withTrashed(['funcaoMinisterial', 'instituicao.instituicaoPai'])
            ->where('pessoa_id', $clerigoId)
            ->whereIn('instituicao_id', $instituicoesPermitidas)
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
        $regiaoId = $this->sessionRegiaoId();
        if (!$this->instituicaoPertenceRegiao((int) $id, $regiaoId)) {
            return [
                'nomeacoes' => collect(),
                'instituicao' => null,
            ];
        }

        $instituicoesPermitidas = $this->instituicoesPermitidas($regiaoId)->pluck('id')->toArray();
        $instituicao = Identifiable::fetchInstituicao($id);
        $nomeacoes = PessoaNomeacao::where('instituicao_id', $id)
            ->whereIn('instituicao_id', $instituicoesPermitidas)
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

    private function instituicoesPermitidas(int $regiaoId): Collection
    {
        return InstituicoesInstituicao::query()
            ->select('id')
            ->where(function ($query) use ($regiaoId) {
                $query->where('id', $regiaoId)
                    ->orWhere('regiao_id', $regiaoId)
                    ->orWhere('instituicao_pai_id', $regiaoId)
                    ->orWhereIn('instituicao_pai_id', function ($subquery) use ($regiaoId) {
                        $subquery->select('id')
                            ->from('instituicoes_instituicoes')
                            ->where('instituicao_pai_id', $regiaoId);
                    });
            })
            ->get();
    }
}
