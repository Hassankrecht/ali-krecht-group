<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AdminHomeSettingController extends Controller
{
    public function edit()
    {
        $settings = HomeSetting::first();
        return view('admins.home-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = HomeSetting::firstOrCreate([]);

        $section = $request->input('section', 'header');

        if ($section === 'banner') {
            $data = $request->validate([
                'banner_enabled'   => 'nullable|boolean',
                'banner_text'      => 'nullable|string|max:255',
                'banner_link'      => 'nullable|url|max:255',
                'banner_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            ]);

            if ($request->hasFile('banner_image')) {
                $settings->banner_image_path = $request->file('banner_image')->store('home', 'public');
            }
            $settings->banner_enabled = $request->boolean('banner_enabled', false);
            $settings->banner_text = $data['banner_text'] ?? $settings->banner_text;
            $settings->banner_link = $data['banner_link'] ?? $settings->banner_link;
            $settings->save();
            return back()->with('success', 'Banner updated.');
        }

        // Header form
        $data = $request->validate([
            'hero_title'       => 'nullable|string|max:255',
            'hero_subtitle'    => 'nullable|string|max:255',
            'hero_media_type'  => 'required|in:image,video',
            'hero_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'hero_video_url'   => 'nullable|url|max:255',
            'hero_video_upload'=> 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:51200',
            'hero_gallery.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'hero_bg_color'    => 'nullable|string|max:20',
            'hero_title_size'  => 'nullable|integer|min:16|max:96',
            'hero_subtitle_size'=> 'nullable|integer|min:12|max:64',
            'hero_button_size' => 'nullable|integer|min:10|max:48',
            'hero_stretch'     => 'nullable|boolean',
            'hero_title_color' => 'nullable|string|max:20',
            'hero_subtitle_color' => 'nullable|string|max:20',
            'hero_title_font'  => 'nullable|string|max:255',
            'hero_subtitle_font'  => 'nullable|string|max:255',
            'theme_primary'    => 'nullable|string|max:20',
            'theme_dark'       => 'nullable|string|max:20',
            'theme_text'       => 'nullable|string|max:20',
            'theme_bg'         => 'nullable|string|max:20',
            'headings_color'   => 'nullable|string|max:20',
            'body_text_color'  => 'nullable|string|max:20',
            'link_color'       => 'nullable|string|max:20',
            'btn_global_primary_color'   => 'nullable|string|max:20',
            'btn_global_primary_style'   => 'nullable|in:solid,outline,pill',
            'btn_global_secondary_color' => 'nullable|string|max:20',
            'btn_global_secondary_style' => 'nullable|in:solid,outline,pill',
            'show_title'      => 'nullable|boolean',
            'show_subtitle'   => 'nullable|boolean',
            'hero_content_pos_x'=> 'nullable|integer|min:0|max:100',
            'hero_content_pos_y'=> 'nullable|integer|min:0|max:100',
            'hero_bg_size'     => 'nullable|integer|min:20|max:400',
            'overlay_enabled'  => 'nullable|boolean',
            'font_family'      => 'nullable|string|max:255',
            'primary_color'    => 'nullable|string|max:20',
            'secondary_color'  => 'nullable|string|max:20',
            'btn_primary_text' => 'nullable|string|max:255',
            'btn_primary_link' => 'nullable|url|max:255',
            'btn_primary_color'=> 'nullable|string|max:20',
            'btn_primary_style'=> 'nullable|in:solid,outline,pill',
            'btn_primary_visible'=> 'nullable|boolean',
            'btn_secondary_text'=> 'nullable|string|max:255',
            'btn_secondary_link'=> 'nullable|url|max:255',
            'btn_secondary_color'=> 'nullable|string|max:20',
            'btn_secondary_style'=> 'nullable|in:solid,outline,pill',
            'btn_secondary_visible'=> 'nullable|boolean',
        ]);

        if ($request->hasFile('hero_image')) {
            $path = $this->storeToAssetsHome($request->file('hero_image'));
            $settings->hero_image_path = $path; // assets/home/<file>
            // صورة منفردة: امسح المعرض والفيديو ليكون الخيار الوحيد
            $settings->hero_gallery = [];
            $settings->hero_video_path = null;
            $settings->hero_video_url = null;
            $settings->hero_media_type = 'image';
        }

        if ($request->hasFile('hero_video_upload')) {
            $videoPath = $this->storeToAssetsHome($request->file('hero_video_upload'));
            $settings->hero_video_path = $videoPath;
            // prefer uploaded video when set
            $settings->hero_media_type = 'video';
            // امسح الصورة والمعرض إذا اخترت فيديو
            $settings->hero_image_path = null;
            $settings->hero_gallery = [];
        }

        if ($request->hasFile('hero_gallery')) {
            // استبدل المعرض بالكامل بالصور الجديدة
            $gallery = [];
            foreach ($request->file('hero_gallery') as $file) {
                $gallery[] = $this->storeToAssetsHome($file);
            }
            $settings->hero_gallery = array_values(array_filter($gallery));
            // عند اختيار معرض صور، نعاملها كصورة ونمسح أي فيديو سابق
            $settings->hero_media_type = 'image';
            $settings->hero_video_path = null;
            $settings->hero_video_url = null;
        }

        $settings->hero_title = $data['hero_title'] ?? $settings->hero_title;
        $settings->hero_subtitle = $data['hero_subtitle'] ?? $settings->hero_subtitle;
        // حسم نوع الوسائط بحيث يكون مصدر واحد فقط: فيديو أو صورة مفردة أو معرض
        $hasVideo = $request->hasFile('hero_video_upload') || !empty($data['hero_video_url']) || !empty($settings->hero_video_path);
        $hasGallery = !empty($settings->hero_gallery);
        $hasImage = !empty($settings->hero_image_path);

        if ($hasVideo) {
            $settings->hero_media_type = 'video';
            $settings->hero_video_url = $data['hero_video_url'] ?? $settings->hero_video_url;
            $settings->hero_image_path = null;
            $settings->hero_gallery = [];
        } elseif ($hasGallery) {
            $settings->hero_media_type = 'image';
            // عند وجود معرض، لا نحتفظ بصورة منفردة
            $settings->hero_image_path = null;
            $settings->hero_video_url = null;
            $settings->hero_video_path = null;
        } elseif ($hasImage) {
            $settings->hero_media_type = 'image';
            $settings->hero_video_url = null;
            $settings->hero_video_path = null;
            $settings->hero_gallery = [];
        } else {
            // fallback: قيمة الـ select إذا لم يتوفر أي ملف/رابط
            $settings->hero_media_type = $data['hero_media_type'];
            if ($settings->hero_media_type === 'image') {
                $settings->hero_video_url = null;
                $settings->hero_video_path = null;
            } else {
                $settings->hero_image_path = null;
                $settings->hero_gallery = [];
            }
        }
        $settings->hero_bg_color = $data['hero_bg_color'] ?? $settings->hero_bg_color ?? '#0b1220';
        $settings->hero_stretch = $request->boolean('hero_stretch', true);
        $settings->hero_title_size = $data['hero_title_size'] ?? $settings->hero_title_size;
        $settings->hero_subtitle_size = $data['hero_subtitle_size'] ?? $settings->hero_subtitle_size;
        $settings->hero_button_size = $data['hero_button_size'] ?? $settings->hero_button_size;
        $settings->hero_title_color = $data['hero_title_color'] ?? $settings->hero_title_color ?? '#ffffff';
        $settings->hero_subtitle_color = $data['hero_subtitle_color'] ?? $settings->hero_subtitle_color ?? '#ffffff';
        $settings->hero_title_font = $data['hero_title_font'] ?? $settings->hero_title_font;
        $settings->hero_subtitle_font = $data['hero_subtitle_font'] ?? $settings->hero_subtitle_font;
        $settings->theme_primary = $data['theme_primary'] ?? $settings->theme_primary;
        $settings->theme_dark = $data['theme_dark'] ?? $settings->theme_dark;
        $settings->theme_text = $data['theme_text'] ?? $settings->theme_text;
        $settings->theme_bg = $data['theme_bg'] ?? $settings->theme_bg;
        $settings->headings_color = $data['headings_color'] ?? $settings->headings_color;
        $settings->body_text_color = $data['body_text_color'] ?? $settings->body_text_color;
        $settings->link_color = $data['link_color'] ?? $settings->link_color;
        $settings->btn_global_primary_color = $data['btn_global_primary_color'] ?? $settings->btn_global_primary_color;
        $settings->btn_global_primary_style = $data['btn_global_primary_style'] ?? $settings->btn_global_primary_style;
        $settings->btn_global_secondary_color = $data['btn_global_secondary_color'] ?? $settings->btn_global_secondary_color;
        $settings->btn_global_secondary_style = $data['btn_global_secondary_style'] ?? $settings->btn_global_secondary_style;
        $settings->show_title = $request->boolean('show_title', true);
        $settings->show_subtitle = $request->boolean('show_subtitle', true);
        $settings->hero_content_pos_x = $data['hero_content_pos_x'] ?? $settings->hero_content_pos_x ?? 10;
        $settings->hero_content_pos_y = $data['hero_content_pos_y'] ?? $settings->hero_content_pos_y ?? 20;
        $settings->hero_bg_size = $data['hero_bg_size'] ?? $settings->hero_bg_size ?? 100;
        $settings->overlay_enabled = $request->boolean('overlay_enabled', false);
        $settings->font_family = $data['font_family'] ?? $settings->font_family;
        $settings->primary_color = $data['primary_color'] ?? $settings->primary_color;
        $settings->secondary_color = $data['secondary_color'] ?? $settings->secondary_color;
        $settings->btn_primary_text = $data['btn_primary_text'] ?? $settings->btn_primary_text;
        $settings->btn_primary_link = $data['btn_primary_link'] ?? $settings->btn_primary_link;
        $settings->btn_primary_color = $data['btn_primary_color'] ?? $settings->btn_primary_color;
        $settings->btn_primary_style = $data['btn_primary_style'] ?? $settings->btn_primary_style ?? 'solid';
        $settings->btn_primary_visible = $request->boolean('btn_primary_visible', true);
        $settings->btn_secondary_text = $data['btn_secondary_text'] ?? $settings->btn_secondary_text;
        $settings->btn_secondary_link = $data['btn_secondary_link'] ?? $settings->btn_secondary_link;
        $settings->btn_secondary_color = $data['btn_secondary_color'] ?? $settings->btn_secondary_color;
        $settings->btn_secondary_style = $data['btn_secondary_style'] ?? $settings->btn_secondary_style ?? 'outline';
        $settings->btn_secondary_visible = $request->boolean('btn_secondary_visible', true);

        $settings->save();

        return back()->with('success', 'Header updated.');
    }

    private function storeToAssetsHome(UploadedFile $file): string
    {
        // احفظ مباشرة تحت public/assets/home (على الخادم = htdocs/public/assets/home)
        // حتى يكون المسار النهائي الذي نُعيده متطابقًا مع مكان الحفظ
        $dir = public_path('assets/home');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = $file->hashName();
        $file->move($dir, $name);
        // أضف "public/" لأن الملفات تحفظ تحت htdocs/public/assets/...
        return 'public/assets/home/' . $name;
    }
}
