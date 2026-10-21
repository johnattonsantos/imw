<?php

namespace App\Services\ServiceEstatisticas;

use App\Support\PeriodoEclesiastico;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MembrosAtivosInativosService
{
    public function datas(int $periodo, Carbon $referencia): array
    {
        [$inicio, $fim] = PeriodoEclesiastico::porQuantidadeAnos($periodo, $referencia);
        if (in_array($periodo, [3, 4, 5], true)) {
            [$inicio] = PeriodoEclesiastico::anuenioCorrente($referencia);
            $inicio->subYears($periodo - 1);
        }
        $datas = [];
        for ($data = $inicio->copy(); $data->lte($fim); $data->addYear()) {
            $fechamento = $data->copy()->addYear()->subDay()->endOfDay();
            $datas[] = [
                'inicio' => $data->toDateString(),
                'fim' => $fechamento->min($fim)->toDateString(),
            ];
        }

        return $datas;
    }

    public function execute(int $regiaoId, int $periodo, string $nivel, Carbon $referencia): array
    {
        $coluna = ['regiao' => 'regiao_id', 'distrito' => 'distrito_id', 'igreja' => 'igreja_id'][$nivel];
        $instituicoes = DB::table('instituicoes_instituicoes')
            ->where(function ($query) use ($regiaoId, $nivel) {
                if ($nivel === 'regiao') {
                    $query->where('id', $regiaoId);
                } else {
                    $query->where('regiao_id', $regiaoId)
                        ->where('tipo_instituicao_id', $nivel === 'distrito' ? 2 : 1);
                }
            })
            ->whereNull('deleted_at')->orderBy('nome')->get(['id', 'nome']);

        $linhas = [];
        foreach ($this->datas($periodo, $referencia) as $intervalo) {
            $fim = $intervalo['fim'];
            $contagensIniciais = $this->contagensNaData($regiaoId, $coluna, $intervalo['inicio']);
            $contagens = $intervalo['inicio'] === $fim ? $contagensIniciais : $this->contagensNaData($regiaoId, $coluna, $fim);

            foreach ($instituicoes as $instituicao) {
                $contagem = $contagens->get($instituicao->id);
                $inicial = $contagensIniciais->get($instituicao->id);
                $ativosIniciais = (int) ($inicial->ativos ?? 0);
                $ativosFinais = (int) ($contagem->ativos ?? 0);
                $linhas[] = [
                    'instituicao' => $instituicao->nome,
                    'inicio' => $intervalo['inicio'],
                    'fim' => $fim,
                    'ativos' => $ativosFinais,
                    'inativos' => (int) ($contagem->inativos ?? 0),
                    'ativos_iniciais' => $ativosIniciais,
                    'crescimento_ativos' => $this->percentualCrescimento($ativosIniciais, $ativosFinais),
                ];
            }
        }

        return $linhas;
    }

    public function percentualCrescimento(int $inicial, int $final): ?float
    {
        if ($inicial === 0) {
            return $final === 0 ? 0.0 : null;
        }

        return round(($final - $inicial) / $inicial * 100, 2);
    }

    private function contagensNaData(int $regiaoId, string $coluna, string $fim): \Illuminate\Support\Collection
    {
        // Reconstruct the status from the last reception known at the snapshot date.
        return DB::table('membresia_rolpermanente as rol')
            ->where('rol.regiao_id', $regiaoId)->whereNull('rol.deleted_at')
            ->where('rol.dt_recepcao', '<=', $fim)
            ->whereNotExists(function ($query) use ($regiaoId, $coluna, $fim) {
                $query->selectRaw('1')->from('membresia_rolpermanente as posterior')
                    ->whereColumn('posterior.membro_id', 'rol.membro_id')
                    ->whereColumn('posterior.'.$coluna, 'rol.'.$coluna)
                    ->where('posterior.regiao_id', $regiaoId)->whereNull('posterior.deleted_at')
                    ->where('posterior.dt_recepcao', '<=', $fim)
                    ->where(function ($query) {
                        $query->whereColumn('posterior.dt_recepcao', '>', 'rol.dt_recepcao')
                            ->orWhere(function ($query) {
                                $query->whereColumn('posterior.dt_recepcao', 'rol.dt_recepcao')
                                    ->whereColumn('posterior.id', '>', 'rol.id');
                            });
                    });
            })
            ->select('rol.'.$coluna.' as instituicao_id')
            ->selectRaw('SUM(CASE WHEN rol.dt_exclusao IS NULL OR rol.dt_exclusao > ? THEN 1 ELSE 0 END) as ativos', [$fim])
            ->selectRaw('SUM(CASE WHEN rol.dt_exclusao <= ? THEN 1 ELSE 0 END) as inativos', [$fim])
            ->groupBy('rol.'.$coluna)->get()->keyBy('instituicao_id');

    }
}
