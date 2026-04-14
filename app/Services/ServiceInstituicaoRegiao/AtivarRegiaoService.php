<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Traits\RegionalScope;
use Illuminate\Support\Facades\Schema;

class AtivarRegiaoService
{
    use RegionalScope;

    public function execute($id)
    {
        $instituicao = InstituicoesInstituicao::withTrashed()
            ->where('id', $id)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();

        $payload = ['ativo' => 1];
        if (Schema::hasColumn('instituicoes_instituicoes', 'data_encerramento')) {
            $payload['data_encerramento'] = null;
        }

        $instituicao->update($payload);

        if ($instituicao->trashed()) {
            $instituicao->restore();
        }
    }
}
