<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->enum('hero_media_type', ['image','video'])->default('image');
            $table->string('hero_image_path')->nullable();
            $table->string('hero_video_url')->nullable();
            $table->json('hero_gallery')->nullable();
            $table->boolean('banner_enabled')->default(false);
            $table->string('banner_text')->nullable();
            $table->string('banner_link')->nullable();
            $table->string('banner_image_path')->nullable();
            $table->string('font_family')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
