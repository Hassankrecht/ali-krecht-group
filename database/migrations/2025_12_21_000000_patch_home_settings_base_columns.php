<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('home_settings')) {
            return;
        }

        Schema::table('home_settings', function (Blueprint $table) {
            // Baseline hero height/width/colors
            if (!Schema::hasColumn('home_settings', 'hero_height')) {
                $table->integer('hero_height')->nullable();
            }
            if (!Schema::hasColumn('home_settings', 'hero_width')) {
                $table->integer('hero_width')->nullable()->after('hero_height');
            }
            if (!Schema::hasColumn('home_settings', 'hero_bg_color')) {
                $table->string('hero_bg_color')->nullable()->after('hero_width');
            }

            // Text controls (sizes/positions)
            if (!Schema::hasColumn('home_settings', 'hero_title_size')) {
                $table->integer('hero_title_size')->nullable()->after('hero_bg_color');
            }
            if (!Schema::hasColumn('home_settings', 'hero_subtitle_size')) {
                $table->integer('hero_subtitle_size')->nullable()->after('hero_title_size');
            }
            if (!Schema::hasColumn('home_settings', 'hero_button_size')) {
                $table->integer('hero_button_size')->nullable()->after('hero_subtitle_size');
            }
            if (!Schema::hasColumn('home_settings', 'hero_content_pos_x')) {
                $table->integer('hero_content_pos_x')->nullable()->after('hero_button_size');
            }
            if (!Schema::hasColumn('home_settings', 'hero_content_pos_y')) {
                $table->integer('hero_content_pos_y')->nullable()->after('hero_content_pos_x');
            }

            // Colors + visibility
            if (!Schema::hasColumn('home_settings', 'hero_title_color')) {
                $table->string('hero_title_color')->nullable()->after('hero_subtitle_size');
            }
            if (!Schema::hasColumn('home_settings', 'hero_subtitle_color')) {
                $table->string('hero_subtitle_color')->nullable()->after('hero_title_color');
            }
            if (!Schema::hasColumn('home_settings', 'show_title')) {
                $table->boolean('show_title')->default(true)->after('hero_content_pos_y');
            }
            if (!Schema::hasColumn('home_settings', 'show_subtitle')) {
                $table->boolean('show_subtitle')->default(true)->after('show_title');
            }

            // Fonts
            if (!Schema::hasColumn('home_settings', 'hero_title_font')) {
                $table->string('hero_title_font')->nullable()->after('hero_title_color');
            }
            if (!Schema::hasColumn('home_settings', 'hero_subtitle_font')) {
                $table->string('hero_subtitle_font')->nullable()->after('hero_subtitle_color');
            }
        });
    }

    public function down(): void
    {
        // لا نحذف الأعمدة في down لتجنب فقدان بيانات إعدادات الواجهة
    }
};
