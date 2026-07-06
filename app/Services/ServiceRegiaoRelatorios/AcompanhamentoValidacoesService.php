<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\MembresiaMembroRecadastramento;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class AcompanhamentoValidacoesService
{
    use Identifiable;

    public function execute(): array
    {
        $regiao = self::fetchtSessionRegiao();

        $igrejas = DB::table('membresia_migracao as mm')
            ->join('instituicoes_instituicoes as ig', 'mm.igreja_id', '=', 'ig.id')
            ->join('instituicoes_instituicoes as di', 'ig.instituicao_pai_id', '=', 'di.id')
            ->join('instituicoes_instituicoes as re', 'di.instituicao_pai_id', '=', 're.id')
            ->whereNull('mm.deleted_at')
            ->where('mm.vinculo', MembresiaMembroRecadastramento::VINCULO_MEMBRO)
            ->where('re.id', $regiao->id)
            ->select([
                're.nome as regiao',
                'di.nome as distrito',
                'ig.nome as igreja',
            ])
            ->selectRaw('COUNT(CASE WHEN mm.validado = 1 THEN mm.id END) as validadas')
            ->selectRaw('COUNT(CASE WHEN COALESCE(mm.validado, 0) = 0 THEN mm.id END) as pendentes')
            ->selectRaw('COUNT(mm.id) as total')
            ->groupBy('re.nome', 'di.nome', 'ig.nome')
            ->orderBy('re.nome')
            ->orderBy('di.nome')
            ->orderBy('ig.nome')
            ->get()
            ->map(function ($igreja) {
                $igreja->validadas = (int) $igreja->validadas;
                $igreja->pendentes = (int) $igreja->pendentes;
                $igreja->total = (int) $igreja->total;
                $igreja->percentual = $igreja->total > 0
                    ? round(($igreja->validadas / $igreja->total) * 100, 2)
                    : 0;

                return $igreja;
            });

        $total = (int) $igrejas->sum('total');
        $validadas = (int) $igrejas->sum('validadas');

        return [
            'regiao' => $regiao,
            'igrejas' => $igrejas,
            'resumo' => (object) [
                'validadas' => $validadas,
                'pendentes' => (int) $igrejas->sum('pendentes'),
                'total' => $total,
                'percentual' => $total > 0 ? round(($validadas / $total) * 100, 2) : 0,
            ],
        ];
    }
}
