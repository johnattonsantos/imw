<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Exceptions\MembroNotFoundException;
use App\Models\PessoasPessoa;
use App\Traits\Identifiable;
use App\Traits\RegionalScope;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class EditarClerigoService
{
    use Identifiable;
    use RegionalScope;

    public function findOne($id)
    {
        $pessoa = PessoasPessoa::where('id', $id)
            ->where('regiao_id', $this->sessionRegiaoId())
            ->firstOrFail();
        

        // Gerar URL temporária para a foto se estiver presente e o bucket for privado
        if ($pessoa->foto) {
            $disk = Storage::disk('s3');
            $pessoa->foto = $disk->temporaryUrl($pessoa->foto, Carbon::now()->addMinutes(1500));
        }
        return $pessoa;
    }
}
