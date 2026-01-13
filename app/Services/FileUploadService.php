<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Store a file on the public disk under the given directory.
     *
     * @return string|null Stored relative path or null on failure.
     */
    public function storePublic(UploadedFile $file, string $directory = 'uploads'): ?string
    {
        try {
            return $file->store($directory, 'public');
        } catch (\Throwable) {
            return null;
        }
    }
}
