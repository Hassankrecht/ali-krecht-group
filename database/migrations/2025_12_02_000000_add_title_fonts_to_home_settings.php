<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('home_settings')) {
            return;
        }

        Schema::table('home_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('home_settings', 'hero_title_font')) {
                $table->string('hero_title_font')->nullable();
            }
            if (!Schema::hasColumn('home_settings', 'hero_subtitle_font')) {
                $table->string('hero_subtitle_font')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('home_settings')) {
            return;
        }
        Schema::table('home_settings', function (Blueprint $table) {
            if (Schema::hasColumn('home_settings', 'hero_title_font')) {
                $table->dropColumn('hero_title_font');
            }
            if (Schema::hasColumn('home_settings', 'hero_subtitle_font')) {
                $table->dropColumn('hero_subtitle_font');
            }
        });
    }
};
