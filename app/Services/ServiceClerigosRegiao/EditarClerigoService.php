<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Exceptions\MembroNotFoundException;
use App\Models\PessoasPessoa;
use App\Traits\Identifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;


class EditarClerigoService
{
    use Identifiable;

    public function findOne($id)
    {
        $pessoa = PessoasPessoa::where('id', $id)->first();
        

        if ($pessoa->foto) {
            $pessoa->foto = $this->resolveFotoUrl((string) $pessoa->foto);
        }
        return $pessoa;
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
            return Storage::disk('s3')->temporaryUrl($foto, Carbon::now()->addMinutes(1500));
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
