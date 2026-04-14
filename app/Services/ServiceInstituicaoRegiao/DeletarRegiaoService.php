<?php

namespace App\Services\ServiceInstituicaoRegiao;

use App\Models\InstituicoesInstituicao;
use App\Traits\RegionalScope;
use Illuminate\Support\Facades\Schema;

class DeletarRegiaoService
{
    use RegionalScope;

    public function execute($id)
    {
        $instituicao = InstituicoesInstituicao::where('id', $id)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();

        $payload = ['ativo' => 0];
        if (Schema::hasColumn('instituicoes_instituicoes', 'data_encerramento') && empty($instituicao->data_encerramento)) {
            $payload['data_encerramento'] = now()->toDateString();
        }

        $instituicao->update($payload);
        $instituicao->delete();
    }
}
