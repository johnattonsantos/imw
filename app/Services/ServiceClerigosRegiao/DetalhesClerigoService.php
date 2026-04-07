<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoasPessoa;
use App\Traits\RegionalScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DetalhesClerigoService
{
    use RegionalScope;

    public function execute($id)
    {
        // Busca o clerigo pelo ID
        $clerigo = PessoasPessoa::select('pessoas_pessoas.*', 'pessoas_status.descricao as situacao', 'formacoes.nivel as formacao')
            ->Leftjoin('pessoas_status', 'pessoas_status.id','pessoas_pessoas.situacao_id')
            ->Leftjoin('formacoes', 'formacoes.id','pessoas_pessoas.formacao_id')
            ->where('pessoas_pessoas.id', $id)
            ->where('pessoas_pessoas.regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();
        if ($clerigo->foto) {
            $disk = Storage::disk('s3');
            $clerigo->foto = $disk->temporaryUrl($clerigo->foto, Carbon::now()->addMinutes(15));
        }
        $clerigo->data_nascimento = formatDate($clerigo->data_nascimento);
        $clerigo->data_consagracao = formatDate($clerigo->data_consagracao);
        $clerigo->data_ordenacao = formatDate($clerigo->data_ordenacao);
        $clerigo->data_integralizacao = formatDate($clerigo->data_integralizacao);


        return $clerigo;
    }
}
