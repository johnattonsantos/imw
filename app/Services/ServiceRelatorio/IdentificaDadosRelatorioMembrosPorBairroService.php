<?php

namespace App\Services\ServiceRelatorio;

use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class IdentificaDadosRelatorioMembrosPorBairroService
{
    use Identifiable;

    public function execute(array $params = []): array
    {
        $localidade = $this->resolveLocalidade($params['localidade'] ?? 'todos');
        $membros = $this->fetchMembrosPorBairro($localidade);

        return [
            'membros' => $membros,
            'localidade' => $localidade,
            'localidadeTexto' => $this->localidadeTexto($localidade),
        ];
    }

    private function fetchMembrosPorBairro(string $localidade)
    {
        return DB::table('membresia_membros as mm')
            ->leftJoin('membresia_contatos as mc', function ($join) {
                $join->on('mc.membro_id', '=', 'mm.id')
                    ->whereNull('mc.deleted_at');
            })
            ->leftJoin('congregacoes_congregacoes as cc', function ($join) {
                $join->on('cc.id', '=', 'mm.congregacao_id')
                    ->whereNull('cc.deleted_at');
            })
            ->select(
                'mm.nome',
                DB::raw("CASE WHEN mm.congregacao_id IS NULL OR mm.congregacao_id = 0 THEN 'Sede' ELSE 'Congregação' END as localidade_tipo"),
                DB::raw("CASE WHEN mm.congregacao_id IS NULL OR mm.congregacao_id = 0 THEN 'Sede' ELSE COALESCE(cc.nome, 'Congregação sem nome') END as localidade_nome"),
                DB::raw("COALESCE(NULLIF(TRIM(mc.bairro), ''), 'Sem bairro informado') as bairro"),
                'mc.cep',
                'mc.endereco',
                'mc.numero',
                'mc.complemento',
                'mc.cidade',
                'mc.estado',
                DB::raw("CASE WHEN mc.telefone_preferencial IS NOT NULL AND mc.telefone_preferencial <> '' THEN mc.telefone_preferencial
                    WHEN mc.telefone_alternativo IS NOT NULL AND mc.telefone_alternativo <> '' THEN mc.telefone_alternativo
                    ELSE mc.telefone_whatsapp END as contato")
            )
            ->where('mm.igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->where('mm.vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->where('mm.status', MembresiaMembro::STATUS_ATIVO)
            ->when($localidade === 'sede', function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('mm.congregacao_id')
                        ->orWhere('mm.congregacao_id', 0);
                });
            })
            ->when($localidade === 'congregacao', function ($query) {
                $query->whereNotNull('mm.congregacao_id')
                    ->where('mm.congregacao_id', '>', 0);
            })
            ->orderBy('bairro')
            ->orderBy('localidade_nome')
            ->orderBy('mm.nome')
            ->get();
    }

    private function resolveLocalidade(string $localidade): string
    {
        return in_array($localidade, ['todos', 'sede', 'congregacao'], true) ? $localidade : 'todos';
    }

    private function localidadeTexto(string $localidade): string
    {
        return [
            'todos' => 'Todos',
            'sede' => 'Sede',
            'congregacao' => 'Congregação',
        ][$localidade] ?? 'Todos';
    }

}
