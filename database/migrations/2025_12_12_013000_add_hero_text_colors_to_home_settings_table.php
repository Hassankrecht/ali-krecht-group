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
            if (!Schema::hasColumn('home_settings', 'hero_title_size')) {
                $table->integer('hero_title_size')->nullable();
            }
            if (!Schema::hasColumn('home_settings', 'hero_subtitle_size')) {
                $table->integer('hero_subtitle_size')->nullable();
            }
            if (!Schema::hasColumn('home_settings', 'hero_title_color')) {
                $table->string('hero_title_color')->nullable();
            }
            if (!Schema::hasColumn('home_settings', 'hero_subtitle_color')) {
                $table->string('hero_subtitle_color')->nullable();
            }
            if (!Schema::hasColumn('home_settings', 'show_title')) {
                $table->boolean('show_title')->default(true);
            }
            if (!Schema::hasColumn('home_settings', 'show_subtitle')) {
                $table->boolean('show_subtitle')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            if (Schema::hasColumn('home_settings', 'hero_title_color')) {
                $table->dropColumn('hero_title_color');
            }
            if (Schema::hasColumn('home_settings', 'hero_subtitle_color')) {
                $table->dropColumn('hero_subtitle_color');
            }
            if (Schema::hasColumn('home_settings', 'show_title')) {
                $table->dropColumn('show_title');
            }
            if (Schema::hasColumn('home_settings', 'show_subtitle')) {
                $table->dropColumn('show_subtitle');
            }
        });
    }
};
