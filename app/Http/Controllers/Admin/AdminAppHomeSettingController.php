<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppHomeSetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AdminAppHomeSettingController extends Controller
{
    public function edit()
    {
        $settings = AppHomeSetting::firstOrCreate([]);

        return view('admins.app-home-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = AppHomeSetting::firstOrCreate([]);

        $data = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'hero_media_type' => 'required|in:image,video,gallery',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10000',
            'hero_image_fit' => 'required|in:cover,contain,fill,fitWidth,fitHeight',
            'hero_video_url' => 'nullable|url|max:255',
            'hero_gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10000',
            'banner_enabled' => 'nullable|boolean',
            'banner_text' => 'nullable|string|max:255',
            'banner_link' => 'nullable|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'theme_mode' => 'required|in:light,dark,auto',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'button_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'font_family' => 'nullable|string|max:255',
            'font_size' => 'nullable|integer|min:8|max:40',
            'overlay_enabled' => 'nullable|boolean',
            'overlay_color' => 'nullable|string|max:20',
            'overlay_opacity' => 'nullable|numeric|min:0|max:1',
            'banner_opacity' => 'nullable|numeric|min:0|max:1',
            'hero_image_opacity' => 'nullable|numeric|min:0|max:1',
            'show_popular_products' => 'nullable|boolean',
            'show_categories' => 'nullable|boolean',
            'show_coupons' => 'nullable|boolean',
        ]);

        if ($request->hasFile('hero_image')) {
            $this->deleteAsset($settings->hero_image_path);
            $settings->hero_image_path = $this->storeToAppHome($request->file('hero_image'));
            $settings->hero_gallery = [];
            $settings->hero_media_type = 'image';
        }

        if ($request->hasFile('hero_gallery')) {
            $this->deleteAssets($settings->hero_gallery ?? []);
            $this->deleteAsset($settings->hero_image_path);
            $gallery = [];
            foreach ($request->file('hero_gallery') as $file) {
                $gallery[] = $this->storeToAppHome($file);
            }
            $settings->hero_gallery = array_values(array_filter($gallery));
            $settings->hero_image_path = null;
            $settings->hero_media_type = 'gallery';
        }

        if ($request->hasFile('banner_image')) {
            $this->deleteAsset($settings->banner_image_path);
            $settings->banner_image_path = $this->storeToAppHome($request->file('banner_image'));
        }

        $settings->hero_title = $data['hero_title'] ?? null;
        $settings->hero_subtitle = $data['hero_subtitle'] ?? null;
        $settings->hero_media_type = $request->hasFile('hero_image') || $request->hasFile('hero_gallery')
            ? $settings->hero_media_type
            : $data['hero_media_type'];
        $settings->hero_video_url = $data['hero_video_url'] ?? null;
        $settings->hero_image_fit = $data['hero_image_fit'] ?? 'contain';
        $settings->banner_enabled = $request->boolean('banner_enabled', false);
        $settings->banner_text = $data['banner_text'] ?? null;
        $settings->banner_link = $data['banner_link'] ?? null;
        $settings->theme_mode = $data['theme_mode'];
        $settings->primary_color = $data['primary_color'] ?? null;
        $settings->secondary_color = $data['secondary_color'] ?? null;
        $settings->button_color = $data['button_color'] ?? null;
        $settings->text_color = $data['text_color'] ?? null;
        $settings->font_family = $data['font_family'] ?? null;
        $settings->font_size = $data['font_size'] ?? null;
        $settings->overlay_enabled = $request->boolean('overlay_enabled', false);
        $settings->overlay_color = $data['overlay_color'] ?? null;
        $settings->overlay_opacity = $data['overlay_opacity'] ?? 0;
        $settings->banner_opacity = $data['banner_opacity'] ?? 1;
        $settings->hero_image_opacity = $data['hero_image_opacity'] ?? 1;
        $settings->show_popular_products = $request->boolean('show_popular_products', true);
        $settings->show_categories = $request->boolean('show_categories', true);
        $settings->show_coupons = $request->boolean('show_coupons', true);
        $settings->save();

        return back()->with('success', 'App home settings updated.');
    }

    private function storeToAppHome(UploadedFile $file): string
    {
        $dir = public_path('assets/app-home');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = $file->hashName();
        $file->move($dir, $name);

        return 'assets/app-home/' . $name;
    }

    private function deleteAsset(?string $path): void
    {
        if (!$path || !str_starts_with($path, 'assets/app-home/')) {
            return;
        }

        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function deleteAssets(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteAsset($path);
        }
    }
}
