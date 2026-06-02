<?php

namespace App\Services\ServiceRelatorio;

use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class IdentificaDadosRelatorioConjugesService
{
    use Identifiable;

    public function execute(): array
    {
        return [
            'membros' => $this->fetchMembrosComConjuge(),
        ];
    }

    private function fetchMembrosComConjuge()
    {
        return MembresiaMembro::select(
                'membresia_membros.nome',
                'membresia_familiares.conjuge_nome',
                DB::raw("CASE WHEN membresia_contatos.telefone_preferencial IS NOT NULL AND membresia_contatos.telefone_preferencial <> '' THEN membresia_contatos.telefone_preferencial
                    WHEN membresia_contatos.telefone_alternativo IS NOT NULL AND membresia_contatos.telefone_alternativo <> '' THEN membresia_contatos.telefone_alternativo
                    ELSE membresia_contatos.telefone_whatsapp END as contato")
            )
            ->join('membresia_familiares', function ($join) {
                $join->on('membresia_familiares.membro_id', 'membresia_membros.id')
                    ->whereNull('membresia_familiares.deleted_at');
            })
            ->leftJoin('membresia_contatos', function ($join) {
                $join->on('membresia_contatos.membro_id', 'membresia_membros.id')
                    ->whereNull('membresia_contatos.deleted_at');
            })
            ->where('membresia_membros.igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->where('membresia_membros.vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->where('membresia_membros.status', MembresiaMembro::STATUS_ATIVO)
            ->whereNotNull('membresia_familiares.conjuge_nome')
            ->where('membresia_familiares.conjuge_nome', '<>', '')
            ->orderBy('membresia_membros.nome')
            ->get();
    }
}
