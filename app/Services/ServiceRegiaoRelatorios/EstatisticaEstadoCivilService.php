<?php

namespace App\Services\ServiceRegiaoRelatorios;

use App\Models\InstituicoesInstituicao;
use App\Models\InstituicoesTipoInstituicao;
use App\Models\MembresiaFormacao;
use App\Models\PessoasPessoa;
use App\Traits\EstatisticaEstadoCivilUtils;
use App\Traits\EstatisticaGeneroUtils;
use App\Traits\Identifiable;
use Carbon\Carbon;

class EstatisticaEstadoCivilService
{
    use EstatisticaGeneroUtils;
    use Identifiable;

    public function execute($distritoId, $vinculo = null)
    {
        $regiao = Identifiable::fetchtSessionRegiao();
        $instituicao = null;
        $vinculosPermitidos = ['C', 'M', 'V'];
        $vinculo = in_array($vinculo, $vinculosPermitidos, true) ? $vinculo : 'M';

        if (!empty($distritoId) && $distritoId !== 'all') {
            $instituicao = InstituicoesInstituicao::where('id', $distritoId)
                ->where('instituicao_pai_id', $regiao->id)
                ->where('tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO)
                ->first();
        }


        return [
            'lancamentos' => EstatisticaEstadoCivilUtils::fetch($distritoId, $regiao->id, $vinculo),
            'distritos'   => Identifiable::fetchDistritosByRegiao($regiao->id),
            'instituicao' => $instituicao,
            'regiao'      => $regiao,
            'vinculo'     => $vinculo,
        ];
    }
}
