<?php

namespace App\Services\ServiceFinanceiro;

use App\Traits\FinanceiroUtils;
use App\Models\Mes;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\DB;

class IdentificaDadosCotaOrcamentariaService
{
    use FinanceiroUtils;

    public function execute($dados)
    { 
        $regiaoId = Identifiable::fetchtSessionRegiao()->id;
        $distritos = DB::table('instituicoes_instituicoes as distrito')
            ->select(
                'distrito.id',
                'distrito.nome as distrito_nome',
            )->join('instituicoes_instituicoes as regiao', function ($join) {
                $join->on('regiao.id', '=', 'distrito.instituicao_pai_id');
            })
            ->where(['regiao.id' => $regiaoId, 'distrito.tipo_instituicao_id' => 2])
            ->whereNull('distrito.data_encerramento')
            ->orderBy('distrito.nome')
            ->get();
        return [
            'cotaOrcamentaria' => FinanceiroUtils::cotasOrcamentarias($dados),
            'meses' => (Object) Mes::get(),
            'distritos' => $distritos
        ];
    }
}
