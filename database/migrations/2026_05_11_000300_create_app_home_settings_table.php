<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('app_home_settings')) {
            return;
        }

        Schema::create('app_home_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->enum('hero_media_type', ['image', 'video', 'gallery'])->default('image');
            $table->string('hero_image_path')->nullable();
            $table->string('hero_video_url')->nullable();
            $table->json('hero_gallery')->nullable();
            $table->boolean('banner_enabled')->default(false);
            $table->string('banner_text')->nullable();
            $table->string('banner_link')->nullable();
            $table->string('banner_image_path')->nullable();
            $table->enum('theme_mode', ['light', 'dark', 'auto'])->default('auto');
            $table->string('primary_color', 20)->nullable();
            $table->string('secondary_color', 20)->nullable();
            $table->string('button_color', 20)->nullable();
            $table->string('text_color', 20)->nullable();
            $table->string('font_family')->nullable();
            $table->unsignedInteger('font_size')->nullable();
            $table->boolean('overlay_enabled')->default(false);
            $table->string('overlay_color', 20)->nullable();
            $table->decimal('overlay_opacity', 3, 2)->default(0.00);
            $table->decimal('banner_opacity', 3, 2)->default(1.00);
            $table->decimal('hero_image_opacity', 3, 2)->default(1.00);
            $table->boolean('show_popular_products')->default(true);
            $table->boolean('show_categories')->default(true);
            $table->boolean('show_coupons')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_home_settings');
    }
};
