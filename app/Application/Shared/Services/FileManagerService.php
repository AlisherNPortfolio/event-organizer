<?php

namespace App\Application\Shared\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileManagerService
{
    public function remove(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    public function upload(UploadedFile $file, string $folder = 'default', string $disk = 'public'): string|bool
    {
        return $file->store($folder, $disk);
    }
}
