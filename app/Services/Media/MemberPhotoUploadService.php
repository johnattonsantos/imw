<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Ramsey\Uuid\Uuid;

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
            Storage::disk(self::DISK)->put($path, $contents, 'public');
        } else {
            $path = $photo->storeAs($directory, $filename, self::DISK);
            Storage::disk(self::DISK)->setVisibility($path, 'public');
        }

        if ($returnPublicUrl) {
            return Storage::disk(self::DISK)->url($path);
        }

        return $path;
    }

    private function compressToTarget(string $sourcePath): string
    {
        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($sourcePath)->orientate();

        $quality = 85;
        $minQuality = 35;

        for ($attempt = 0; $attempt < 24; $attempt++) {
            $encoded = $image->encode('jpg', $quality);
            $binary = (string) $encoded;

            if (strlen($binary) <= self::MAX_PHOTO_BYTES) {
                return $binary;
            }

            if ($quality > $minQuality) {
                $quality -= 5;
                continue;
            }

            $newWidth = (int) floor($image->width() * 0.9);
            $newHeight = (int) floor($image->height() * 0.9);

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

