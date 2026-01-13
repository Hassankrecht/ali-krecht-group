<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->integer('hero_title_size')->nullable()->after('hero_bg_color'); // px
            $table->integer('hero_subtitle_size')->nullable()->after('hero_title_size'); // px
            $table->integer('hero_button_size')->nullable()->after('hero_subtitle_size'); // px
            $table->integer('hero_content_pos_x')->nullable()->after('hero_button_size'); // %
            $table->integer('hero_content_pos_y')->nullable()->after('hero_content_pos_x'); // %
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title_size',
                'hero_subtitle_size',
                'hero_button_size',
                'hero_content_pos_x',
                'hero_content_pos_y',
            ]);
        });
    }
};
