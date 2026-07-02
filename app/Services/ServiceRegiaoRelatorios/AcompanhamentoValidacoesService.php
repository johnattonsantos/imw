<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\InstituicoesTipoInstituicao;
use App\Models\MembresiaMembroRecadastramento;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class AcompanhamentoValidacoesService
{
    use Identifiable;

    public function execute(): array
    {
        $regiao = self::fetchtSessionRegiao();

        $totaisPorIgreja = DB::table('vw_rol_membros_recadastro as rol')
            ->join('membresia_migracao as membro', function ($join) {
                $join->on('membro.id', '=', 'rol.membro_id')
                    ->whereNull('membro.deleted_at');
            })
            ->where('membro.vinculo', MembresiaMembroRecadastramento::VINCULO_MEMBRO)
            ->selectRaw('rol.igreja_id')
            ->selectRaw('SUM(CASE WHEN membro.validado = 1 THEN 1 ELSE 0 END) as validadas')
            ->selectRaw('SUM(CASE WHEN COALESCE(membro.validado, 0) = 0 THEN 1 ELSE 0 END) as pendentes')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('rol.igreja_id');

        $igrejas = DB::table('instituicoes_instituicoes as igreja')
            ->join('instituicoes_instituicoes as distrito', function ($join) use ($regiao) {
                $join->on('distrito.id', '=', 'igreja.instituicao_pai_id')
                    ->where('distrito.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
                    ->where('distrito.instituicao_pai_id', $regiao->id);
            })
            ->leftJoinSub($totaisPorIgreja, 'validacoes', function ($join) {
                $join->on('validacoes.igreja_id', '=', 'igreja.id');
            })
            ->where('igreja.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL)
            ->where('igreja.ativo', 1)
            ->where('distrito.ativo', 1)
            ->select([
                'distrito.nome as distrito',
                'igreja.nome as igreja',
            ])
            ->selectRaw('COALESCE(validacoes.validadas, 0) as validadas')
            ->selectRaw('COALESCE(validacoes.pendentes, 0) as pendentes')
            ->selectRaw('COALESCE(validacoes.total, 0) as total')
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->get()
            ->map(function ($igreja) use ($regiao) {
                $igreja->regiao = $regiao->nome;
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
