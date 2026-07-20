<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\PessoasPessoa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DetalhesClerigoService
{
    public function execute($id)
    {
        // Busca o clerigo pelo ID
        $clerigo = PessoasPessoa::select('pessoas_pessoas.*', 'pessoas_status.descricao as situacao', 'formacoes.nivel as formacao')
            ->Leftjoin('pessoas_status', 'pessoas_status.id','pessoas_pessoas.situacao_id')
            ->Leftjoin('formacoes', 'formacoes.id','pessoas_pessoas.formacao_id')
            ->findOrFail($id);
        if ($clerigo->foto) {
            $clerigo->foto = $this->resolveFotoUrl((string) $clerigo->foto);
        }
        $clerigo->data_nascimento = formatDate($clerigo->data_nascimento);
        $clerigo->data_consagracao = formatDate($clerigo->data_consagracao);
        $clerigo->data_ordenacao = formatDate($clerigo->data_ordenacao);
        $clerigo->data_integralizacao = formatDate($clerigo->data_integralizacao);


        return $clerigo;
    }

    private function resolveFotoUrl(string $foto): string
    {
        if (Str::startsWith($foto, ['http://', 'https://'])) {
            return $foto;
        }

        if (Str::startsWith($foto, ['/storage/', 'storage/'])) {
            return Str::startsWith($foto, '/') ? $foto : '/' . ltrim($foto, '/');
        }

        if (!$this->hasS3Credentials()) {
            return asset('theme/images/sem-foto.jpg');
        }

        try {
            return Storage::disk('s3')->temporaryUrl($foto, Carbon::now()->addMinutes(15));
        } catch (\Throwable $e) {
            return asset('theme/images/sem-foto.jpg');
        }
    }

    private function hasS3Credentials(): bool
    {
        return filled(Config::get('filesystems.disks.s3.key'))
            && filled(Config::get('filesystems.disks.s3.secret'))
            && filled(Config::get('filesystems.disks.s3.region'))
            && filled(Config::get('filesystems.disks.s3.bucket'));
    }
}
