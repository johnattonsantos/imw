<?php

namespace App\Services\ServiceClerigosRegiao;

use App\Models\InstituicoesInstituicao;
use App\Models\PessoasPessoa;
use App\Services\ServicePerfil\ListPerfilService;
use App\Support\SimpleQrCode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CarteirasClerigosService
{
    public function listar(int $regiaoId, ?array $ids = null): Collection
    {
        return PessoasPessoa::query()
            ->leftJoin('pessoas_status as situacao', 'situacao.id', '=', 'pessoas_pessoas.situacao_id')
            ->where('pessoas_pessoas.regiao_id', $regiaoId)
            ->when($ids !== null, fn ($query) => $query->whereIn('pessoas_pessoas.id', $ids))
            ->orderBy('pessoas_pessoas.nome')
            ->get(['pessoas_pessoas.*', 'situacao.descricao as situacao_nome']);
    }

    public function preparar(Collection $clerigos, InstituicoesInstituicao $regiao): Collection
    {
        $contexto = app(ListPerfilService::class)->contextoCarteira($regiao);

        return $clerigos->map(function ($clerigo) use ($contexto) {
            $carteira = array_merge($clerigo->toArray(), $contexto);
            $carteira['foto_pdf'] = $this->fotoDataUri($clerigo->foto);
            $carteira['validacao_url'] = URL::signedRoute('validar-clerigo.show', ['clerigo' => $clerigo->id]);
            $carteira['qr_code'] = SimpleQrCode::pngDataUri($carteira['validacao_url'], 5);

            return $carteira;
        });
    }

    public function background(): string
    {
        return 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('theme/images/carteira-digital.png')));
    }

    private function fotoDataUri(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        try {
            if (Str::startsWith($path, ['https://', 'http://'])) {
                $response = Http::timeout(5)->get($path);
                return $response->successful() ? $this->imagemDataUri($response->body()) : null;
            }

            $path = ltrim($path, '/');
            foreach ([public_path($path), storage_path('app/public/'.Str::after($path, 'storage/'))] as $candidate) {
                $real = realpath($candidate);
                if ($real && (str_starts_with($real, public_path().DIRECTORY_SEPARATOR)
                    || str_starts_with($real, storage_path('app/public').DIRECTORY_SEPARATOR)) && is_file($real)) {
                    return $this->imagemDataUri(file_get_contents($real));
                }
            }

            return $this->imagemDataUri(Storage::disk('s3')->get($path));
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function imagemDataUri(?string $contents): ?string
    {
        $size = $contents ? @getimagesizefromstring($contents) : false;
        if (!$size || !in_array($size['mime'], ['image/jpeg', 'image/png', 'image/gif'], true)) {
            return null;
        }

        return 'data:'.$size['mime'].';base64,'.base64_encode($contents);
    }
}
