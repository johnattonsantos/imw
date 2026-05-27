<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class MemberPhotoUploadService
{
    private const DISK = 's3';
    private const MAX_PHOTO_BYTES = 204800; // 200 KB

    public function upload(UploadedFile $photo, string $directory = 'fotos', bool $returnPublicUrl = false): string
    {
        $directory = trim($directory, '/');
        $extension = strtolower($photo->getClientOriginalExtension() ?: 'jpg');
        $filename = Uuid::uuid4()->toString() . '.' . $extension;
        $path = $directory . '/' . $filename;

        if ((int) $photo->getSize() > self::MAX_PHOTO_BYTES) {
            $contents = $this->compressToTarget($photo->getRealPath() ?: $photo->getPathname());
            $path = $directory . '/' . Uuid::uuid4()->toString() . '.jpg';
            $stored = Storage::disk(self::DISK)->put($path, $contents, [
                'ContentType' => 'image/jpeg',
            ]);
            if ($stored === true) {
                return $returnPublicUrl ? (string) Storage::disk(self::DISK)->url($path) : $path;
            }

            throw new RuntimeException('Não foi possível enviar a foto para o S3 no momento.');
        }

        $path = $photo->storeAs($directory, $filename, [
            'disk' => self::DISK,
        ]);

        if (is_string($path) && $path !== '') {
            return $returnPublicUrl ? (string) Storage::disk(self::DISK)->url($path) : $path;
        }

        throw new RuntimeException('Não foi possível enviar a foto para o S3 no momento.');
    }

    private function compressToTarget(string $sourcePath): string
    {
        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($sourcePath)->orientate();

        if ($image->width() > 1600 || $image->height() > 1600) {
            $image->resize(1600, 1600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $quality = 78;
        $minQuality = 42;

        for ($attempt = 0; $attempt < 12; $attempt++) {
            $encoded = $image->encode('jpg', $quality);
            $binary = (string) $encoded;

            if (strlen($binary) <= self::MAX_PHOTO_BYTES) {
                return $binary;
            }

            if ($quality > $minQuality) {
                $quality -= 6;
                continue;
            }

            $newWidth = (int) floor($image->width() * 0.85);
            $newHeight = (int) floor($image->height() * 0.85);

            if ($newWidth < 320 || $newHeight < 320) {
                return $binary;
            }

            $image->resize($newWidth, $newHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        return (string) $image->encode('jpg', $minQuality);
    }
}
