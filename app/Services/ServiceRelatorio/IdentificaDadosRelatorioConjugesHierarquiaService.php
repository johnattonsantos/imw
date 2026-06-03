<?php

namespace App\Services\ServiceRelatorio;

use App\Models\InstituicoesInstituicao;
use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class IdentificaDadosRelatorioConjugesHierarquiaService
{
    use Identifiable;

    public function executeDistrito(): array
    {
        $distritoId = session()->get('session_perfil')->instituicao_id;
        $distrito = InstituicoesInstituicao::find($distritoId);
        $membros = $this->fetchMembrosComConjuge('distrito', $distritoId);

        return [
            'nivel' => 'distrito',
            'titulo' => 'RELATÓRIO DE CÔNJUGES - ' . optional($distrito)->nome,
            'instituicaoNome' => optional($distrito)->nome,
            'membros' => $membros,
            'totaisIgrejas' => $this->totaisPorIgreja($membros),
            'totaisDistritos' => collect(),
        ];
    }

    public function executeRegiao(): array
    {
        $regiao = Identifiable::fetchtSessionRegiao();
        $membros = $this->fetchMembrosComConjuge('regiao', $regiao->id);

        return [
            'nivel' => 'regiao',
            'titulo' => 'RELATÓRIO DE CÔNJUGES - ' . $regiao->nome,
            'instituicaoNome' => $regiao->nome,
            'membros' => $membros,
            'totaisIgrejas' => $this->totaisPorIgreja($membros),
            'totaisDistritos' => $this->totaisPorDistrito($membros),
        ];
    }

    private function fetchMembrosComConjuge(string $nivel, int $instituicaoId)
    {
        return DB::table('membresia_membros as mm')
            ->select(
                'dist.nome as distrito_nome',
                'igreja.nome as igreja_nome',
                'mm.nome as membro_nome',
                'mf.conjuge_nome',
                'mf.data_casamento',
                DB::raw("CASE WHEN mc.telefone_preferencial IS NOT NULL AND mc.telefone_preferencial <> '' THEN mc.telefone_preferencial
                    WHEN mc.telefone_alternativo IS NOT NULL AND mc.telefone_alternativo <> '' THEN mc.telefone_alternativo
                    ELSE mc.telefone_whatsapp END as contato")
            )
            ->join('membresia_familiares as mf', function ($join) {
                $join->on('mf.membro_id', 'mm.id')
                    ->whereNull('mf.deleted_at');
            })
            ->leftJoin('membresia_contatos as mc', function ($join) {
                $join->on('mc.membro_id', 'mm.id')
                    ->whereNull('mc.deleted_at');
            })
            ->leftJoin('instituicoes_instituicoes as igreja', 'igreja.id', 'mm.igreja_id')
            ->leftJoin('instituicoes_instituicoes as dist', 'dist.id', 'igreja.instituicao_pai_id')
            ->where('mm.vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->where('mm.status', MembresiaMembro::STATUS_ATIVO)
            ->whereNotNull('mf.conjuge_nome')
            ->where('mf.conjuge_nome', '<>', '')
            ->when($nivel === 'distrito', fn ($query) => $query->where('igreja.instituicao_pai_id', $instituicaoId))
            ->when($nivel === 'regiao', fn ($query) => $query->where('dist.instituicao_pai_id', $instituicaoId))
            ->orderBy('dist.nome')
            ->orderBy('igreja.nome')
            ->orderBy('mm.nome')
            ->get();
    }

    private function totaisPorIgreja($membros)
    {
        return $membros
            ->groupBy(fn ($membro) => ($membro->distrito_nome ?? '-') . '|' . ($membro->igreja_nome ?? '-'))
            ->map(function ($items) {
                $first = $items->first();

                return (object) [
                    'distrito_nome' => $first->distrito_nome ?? '-',
                    'igreja_nome' => $first->igreja_nome ?? '-',
                    'total' => $items->count(),
                ];
            })
            ->values();
    }

    private function totaisPorDistrito($membros)
    {
        return $membros
            ->groupBy(fn ($membro) => $membro->distrito_nome ?? '-')
            ->map(function ($items) {
                return (object) [
                    'distrito_nome' => $items->first()->distrito_nome ?? '-',
                    'total' => $items->count(),
                ];
            })
            ->values();
    }
}
