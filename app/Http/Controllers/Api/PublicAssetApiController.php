<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class PublicAssetApiController extends Controller
{
    public function show(string $path)
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (str_contains($path, '..')) {
            abort(404);
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (!str_starts_with($path, 'assets/') && !str_starts_with($path, 'storage/')) {
            abort(404);
        }

        $fullPath = public_path($path);

        if (!File::isFile($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath, [
            'Access-Control-Allow-Origin' => '*',
            'Cross-Origin-Resource-Policy' => 'cross-origin',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}