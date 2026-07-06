<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class PerfilMembrosRecebidosService
{
    use Identifiable;

    public function execute(string $dataInicial, string $dataFinal): array
    {
        $regiao = self::fetchtSessionRegiao();
        $idade = 'TIMESTAMPDIFF(YEAR, mm.data_nascimento, CURDATE())';

        $membros = DB::table('membresia_rolpermanente as mr')
            ->join('membresia_membros as mm', function ($join) {
                $join->on('mm.id', '=', 'mr.membro_id')
                    ->whereNull('mm.deleted_at');
            })
            ->join('instituicoes_instituicoes as igreja', 'igreja.id', '=', 'mr.igreja_id')
            ->join('instituicoes_instituicoes as distrito', 'distrito.id', '=', 'igreja.instituicao_pai_id')
            ->join('instituicoes_instituicoes as regiao', 'regiao.id', '=', 'distrito.instituicao_pai_id')
            ->leftJoin('membresia_formacoes as escolaridade', function ($join) {
                $join->on('escolaridade.id', '=', 'mm.escolaridade_id')
                    ->whereNull('escolaridade.deleted_at');
            })
            ->leftJoin('membresia_funcoeseclesiasticas as funcao', function ($join) {
                $join->on('funcao.id', '=', 'mm.funcao_eclesiastica_id')
                    ->whereNull('funcao.deleted_at');
            })
            ->leftJoin('membresia_situacoes as modo', function ($join) {
                $join->on('modo.id', '=', 'mr.modo_recepcao_id')
                    ->whereNull('modo.deleted_at');
            })
            ->whereNull('mr.deleted_at')
            ->where('mm.vinculo', MembresiaMembro::VINCULO_MEMBRO)
            ->where('regiao.id', $regiao->id)
            ->whereBetween('mr.dt_recepcao', [$dataInicial, $dataFinal])
            ->select([
                'distrito.nome as distrito',
                'igreja.nome as igreja',
                'mm.nome as membro',
                'mm.sexo',
                'mm.data_nascimento',
                'escolaridade.descricao as escolaridade',
                'funcao.descricao as funcao_eclesiastica',
                'modo.nome as modo_recepcao',
                'mr.dt_recepcao as data_recepcao',
            ])
            ->selectRaw("{$idade} as idade")
            ->selectRaw("CASE
                WHEN mm.data_nascimento IS NULL OR mm.data_nascimento > CURDATE() THEN 'Não informado'
                WHEN {$idade} BETWEEN 0 AND 9 THEN 'Kid'
                WHEN {$idade} BETWEEN 10 AND 13 THEN 'Conexão'
                WHEN {$idade} BETWEEN 14 AND 17 THEN 'Fire'
                WHEN {$idade} BETWEEN 18 AND 29 THEN 'Move'
                WHEN {$idade} BETWEEN 30 AND 59 AND mm.sexo = 'M' THEN 'Homens'
                WHEN {$idade} BETWEEN 30 AND 59 AND mm.sexo = 'F' THEN 'Mulheres'
                WHEN {$idade} BETWEEN 30 AND 59 THEN 'Adultos'
                WHEN {$idade} >= 60 THEN '60+'
                ELSE 'Não informado'
            END as ministerio")
            ->selectRaw("CASE UPPER(TRIM(COALESCE(mm.estado_civil, '')))
                WHEN 'S' THEN 'Solteiro'
                WHEN 'C' THEN 'Casado'
                WHEN 'D' THEN 'Divorciado'
                WHEN 'V' THEN 'Viúvo'
                ELSE 'Não informado'
            END as estado_civil")
            ->orderByDesc('mr.dt_recepcao')
            ->orderBy('distrito.nome')
            ->orderBy('igreja.nome')
            ->orderBy('mm.nome')
            ->get();

        return [
            'regiao' => $regiao,
            'membros' => $membros,
            'dataInicial' => $dataInicial,
            'dataFinal' => $dataFinal,
        ];
    }
}
