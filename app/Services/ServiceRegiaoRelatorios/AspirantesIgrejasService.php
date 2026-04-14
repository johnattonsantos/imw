<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class AspirantesIgrejasService
{
    public function execute(array $params = []): array
    {
        $regiao = Identifiable::fetchtSessionRegiao();
        $distritoId = $params['distrito_id'] ?? 'all';
        $igrejaId = $params['igreja_id'] ?? 'all';

        $deveBuscar = isset($params['action']) || isset($params['buscar']);

        return [
            'distritos' => Identifiable::fetchDistritosByRegiao($regiao->id),
            'igrejas' => $this->fetchIgrejas($regiao->id, $distritoId),
            'aspirantes' => $deveBuscar
                ? $this->fetchAspirantes($regiao->id, $distritoId, $igrejaId)
                : collect(),
        ];
    }

    private function fetchIgrejas(int $regiaoId, string $distritoId)
    {
        return DB::table('instituicoes_instituicoes as igreja')
            ->join('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->select('igreja.id', 'igreja.nome')
            ->where('distrito.instituicao_pai_id', $regiaoId)
            ->where('distrito.tipo_instituicao_id', 2)
            ->where('igreja.tipo_instituicao_id', 1)
            ->when($distritoId !== 'all' && is_numeric($distritoId), function ($query) use ($distritoId) {
                $query->where('distrito.id', (int) $distritoId);
            })
            ->orderBy('igreja.nome')
            ->get();
    }

    private function fetchAspirantes(int $regiaoId, string $distritoId, string $igrejaId)
    {
        return DB::table('instituicoes_instituicoes as igreja')
            ->join('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->join('membresia_membros as mm', 'mm.igreja_id', '=', 'igreja.id')
            ->leftJoin('membresia_contatos as mc', 'mc.membro_id', '=', 'mm.id')
            ->select(
                'distrito.nome as distrito_nome',
                'igreja.nome as igreja_nome',
                'mm.nome as membro_nome',
                'mm.sexo',
                'mm.estado_civil',
                'mm.cpf',
                DB::raw("DATE_FORMAT(mm.data_nascimento, '%d/%m/%Y') as data_nascimento"),
                DB::raw("CASE WHEN mc.telefone_preferencial IS NOT NULL AND mc.telefone_preferencial <> '' THEN mc.telefone_preferencial
                              WHEN mc.telefone_whatsapp IS NOT NULL AND mc.telefone_whatsapp <> '' THEN mc.telefone_whatsapp
                              WHEN mc.telefone_alternativo IS NOT NULL AND mc.telefone_alternativo <> '' THEN mc.telefone_alternativo
                              ELSE '' END as contato"),
                DB::raw("CASE WHEN mc.email_preferencial IS NOT NULL AND mc.email_preferencial <> '' THEN mc.email_preferencial
                              WHEN mc.email_alternativo IS NOT NULL AND mc.email_alternativo <> '' THEN mc.email_alternativo
                              ELSE '' END as email"),
                DB::raw("CASE WHEN mm.igreja_host IS NOT NULL AND mm.igreja_host <> '' THEN mm.igreja_host ELSE igreja.nome END as igreja_origem")
            )
            ->where('distrito.instituicao_pai_id', $regiaoId)
            ->where('distrito.tipo_instituicao_id', 2)
            ->where('igreja.tipo_instituicao_id', 1)
            ->where('mm.funcao_eclesiastica_id', 7)
            ->whereNull('mm.deleted_at')
            ->when($distritoId !== 'all' && is_numeric($distritoId), function ($query) use ($distritoId) {
                $query->where('distrito.id', (int) $distritoId);
            })
            ->when($igrejaId !== 'all' && is_numeric($igrejaId), function ($query) use ($igrejaId) {
                $query->where('igreja.id', (int) $igrejaId);
            })
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->orderBy('mm.nome')
            ->get();
    }
}
