<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppHomeSettingResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'hero_media_type' => $this->hero_media_type,
            'hero_image_path' => $this->hero_image_path,
            'hero_image_fit' => $this->hero_image_fit ?? 'contain',
            'hero_image_url' => $this->assetUrl($this->hero_image_path),
            'hero_video_url' => $this->hero_video_url,
            'hero_gallery' => $this->hero_gallery ?? [],
            'hero_gallery_urls' => collect($this->hero_gallery ?? [])
                ->map(fn ($path) => $this->assetUrl($path))
                ->filter()
                ->values(),
            'banner_enabled' => (bool) $this->banner_enabled,
            'banner_text' => $this->banner_text,
            'banner_link' => $this->banner_link,
            'banner_image_path' => $this->banner_image_path,
            'banner_image_url' => $this->assetUrl($this->banner_image_path),
            'theme_mode' => $this->theme_mode,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'button_color' => $this->button_color,
            'text_color' => $this->text_color,
            'font_family' => $this->font_family,
            'font_size' => $this->font_size,
            'overlay_enabled' => (bool) $this->overlay_enabled,
            'overlay_color' => $this->overlay_color,
            'overlay_opacity' => (float) $this->overlay_opacity,
            'banner_opacity' => (float) $this->banner_opacity,
            'hero_image_opacity' => (float) $this->hero_image_opacity,
            'show_popular_products' => (bool) $this->show_popular_products,
            'show_categories' => (bool) $this->show_categories,
            'show_coupons' => (bool) $this->show_coupons,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        if (str_starts_with($path, 'assets/') || str_starts_with($path, 'storage/')) {
            return url('api/media/' . $path);
        }

        return url('api/media/storage/' . $path);
    }
}