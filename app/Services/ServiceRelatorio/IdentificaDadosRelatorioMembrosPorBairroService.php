<?php

namespace App\Services\ServiceRelatorio;

use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class IdentificaDadosRelatorioMembrosPorBairroService
{
    use Identifiable;

    public function execute(): array
    {
        $membros = $this->fetchMembrosPorBairro();

        return [
            'membros' => $membros,
        ];
    }

    private function fetchMembrosPorBairro()
    {
        return DB::table('membresia_membros as mm')
            ->leftJoin('membresia_contatos as mc', function ($join) {
                $join->on('mc.membro_id', '=', 'mm.id')
                    ->whereNull('mc.deleted_at');
            })
            ->select(
                'mm.nome',
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
            ->orderBy('bairro')
            ->orderBy('mm.nome')
            ->get();
    }

}
