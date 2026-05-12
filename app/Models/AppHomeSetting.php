<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppHomeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_media_type',
        'hero_image_path',
        'hero_image_fit',
        'hero_video_url',
        'hero_gallery',
        'banner_enabled',
        'banner_text',
        'banner_link',
        'banner_image_path',
        'theme_mode',
        'primary_color',
        'secondary_color',
        'button_color',
        'text_color',
        'font_family',
        'font_size',
        'overlay_enabled',
        'overlay_color',
        'overlay_opacity',
        'banner_opacity',
        'hero_image_opacity',
        'show_popular_products',
        'show_categories',
        'show_coupons',
    ];

    protected $casts = [
        'hero_gallery' => 'array',
        'banner_enabled' => 'boolean',
        'font_size' => 'integer',
        'overlay_enabled' => 'boolean',
        'overlay_opacity' => 'float',
        'banner_opacity' => 'float',
        'hero_image_opacity' => 'float',
        'show_popular_products' => 'boolean',
        'show_categories' => 'boolean',
        'show_coupons' => 'boolean',
    ];
}
