<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class GalleryController extends Controller
{
    /**
     * Simple filesystem-based gallery reader.
     * Structure: public/assets/gallery/<parent>/<child>/<images>
     */
    public function index()
    {
        $basePath = public_path('assets/gallery');
        $parents = [];

        if (File::exists($basePath)) {
            foreach (File::directories($basePath) as $parentDir) {
                $parentName = basename($parentDir);
                $children = [];

                foreach (File::directories($parentDir) as $childDir) {
                    $childName = basename($childDir);
                    $images = collect(File::files($childDir))
                        ->filter(fn($f) => in_array(strtolower($f->getExtension()), ['jpg','jpeg','png','webp']))
                        ->map(fn($f) => asset('assets/gallery/' . $parentName . '/' . $childName . '/' . $f->getFilename()))
                        ->values()
                        ->toArray();

                    $children[] = [
                        'name' => $childName,
                        'slug' => strtolower(str_replace(' ', '-', $childName)),
                        'images' => $images,
                    ];
                }

                $parents[] = [
                    'name' => $parentName,
                    'slug' => strtolower(str_replace(' ', '-', $parentName)),
                    'children' => $children,
                ];
            }
        }

        return view('gallery', [
            'parents' => collect($parents),
        ]);
    }
}
