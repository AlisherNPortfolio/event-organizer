<?php

namespace App\Application\Shared\Services;

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
}
