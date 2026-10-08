<?php

namespace App\Services\ServiceClerigosRegiao;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PesquisarClerigosPorCpfService
{
    public function execute(string $cpf, int $regiaoId): array
    {
        // Exact CPF matching includes legacy formatted values and inactive registrations.
        $clerigos = DB::table('pessoas_pessoas as pessoa')
            ->leftJoin('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'pessoa.regiao_id')
            ->leftJoin('pessoas_status as situacao', 'situacao.id', '=', 'pessoa.situacao_id')
            ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(pessoa.cpf, '.', ''), '-', ''), '/', ''), ' ', '') = ?", [$cpf])
            ->select([
                'pessoa.nome', 'pessoa.cpf', 'pessoa.regiao_id', 'pessoa.deleted_at',
                'regiao.nome as regiao_nome', 'situacao.descricao as situacao_nome',
            ])
            ->orderBy('regiao.nome')->orderBy('pessoa.nome')->get();

        return $this->separarPorRegiao($clerigos, $regiaoId);
    }

    public function separarPorRegiao(Collection $clerigos, int $regiaoId): array
    {
        return [
            'daRegiao' => $clerigos->filter(fn ($clerigo) => (int) $clerigo->regiao_id === $regiaoId)->values(),
            'outrasRegioes' => $clerigos->reject(fn ($clerigo) => (int) $clerigo->regiao_id === $regiaoId)->values(),
            'multiplasRegioes' => $clerigos->pluck('regiao_id')->filter()->unique()->count() > 1,
            'duplicadoNaRegiao' => $clerigos->filter(fn ($clerigo) => (int) $clerigo->regiao_id === $regiaoId)->count() > 1,
        ];
    }
}
