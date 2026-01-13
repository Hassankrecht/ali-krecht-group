<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_media_type',
        'hero_image_path',
        'hero_video_url',
        'hero_video_path',
        'hero_gallery',
        'hero_height',
        'hero_width',
        'hero_auto_fit',
        'hero_stretch',
        'hero_bg_color',
        'hero_title_size',
        'hero_subtitle_size',
        'hero_button_size',
        'hero_title_color',
        'hero_subtitle_color',
        'hero_title_font',
        'hero_subtitle_font',
        'theme_primary',
        'theme_dark',
        'theme_text',
        'theme_bg',
        'headings_color',
        'body_text_color',
        'link_color',
        'btn_global_primary_color',
        'btn_global_primary_style',
        'btn_global_secondary_color',
        'btn_global_secondary_style',
        'show_title',
        'show_subtitle',
        'hero_content_pos_x',
        'hero_content_pos_y',
        'hero_bg_size',
        'overlay_color',
        'overlay_opacity',
        'overlay_enabled',
        'banner_enabled',
        'banner_text',
        'banner_link',
        'banner_image_path',
        'font_family',
        'primary_color',
        'secondary_color',
        'btn_primary_text',
        'btn_primary_link',
        'btn_primary_color',
        'btn_primary_style',
        'btn_primary_visible',
        'btn_secondary_text',
        'btn_secondary_link',
        'btn_secondary_color',
        'btn_secondary_style',
        'btn_secondary_visible',
    ];

    protected $casts = [
        'hero_gallery' => 'array',
        'banner_enabled' => 'boolean',
        'btn_primary_visible' => 'boolean',
        'btn_secondary_visible' => 'boolean',
        'overlay_enabled' => 'boolean',
        'hero_auto_fit' => 'boolean',
    ];
}
