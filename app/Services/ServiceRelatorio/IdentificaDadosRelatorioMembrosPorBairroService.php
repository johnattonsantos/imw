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
        $congregacoes = Identifiable::fetchCongregacoes()
            ->sortBy('nome', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
        $localidade = $this->resolveLocalidade($params['localidade'] ?? 'sede', $congregacoes);
        $membros = $this->fetchMembrosPorBairro($localidade);

        return [
            'membros' => $membros,
            'congregacoes' => $congregacoes,
            'localidade' => $localidade,
            'localidadeTexto' => $this->localidadeTexto($localidade, $congregacoes),
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
                $join->on('cc.id', '=', 'mm.congregacao_id');
            })
            ->select(
                'mm.nome',
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
            ->when($localidade !== 'sede', function ($query) use ($localidade) {
                $query->where('mm.congregacao_id', (int) $localidade);
            })
            ->orderBy('bairro')
            ->orderBy('localidade_nome')
            ->orderBy('mm.nome')
            ->get();
    }

    private function resolveLocalidade(string $localidade, $congregacoes): string
    {
        if ($localidade === 'sede') {
            return 'sede';
        }

        if (ctype_digit($localidade) && $congregacoes->contains('id', (int) $localidade)) {
            return $localidade;
        }

        return 'sede';
    }

    private function localidadeTexto(string $localidade, $congregacoes): string
    {
        if ($localidade === 'sede') {
            return 'Sede';
        }

        $congregacao = $congregacoes->firstWhere('id', (int) $localidade);

        return $congregacao ? $congregacao->nome : 'Sede';
    }

}
