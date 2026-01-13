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
            // تأكد من وجود أعمدة الخطوط الأساسية قبل الإضافة
            if (!Schema::hasColumn('home_settings', 'hero_title_font')) {
                $table->string('hero_title_font')->nullable();
            }
            if (!Schema::hasColumn('home_settings', 'hero_subtitle_font')) {
                $table->string('hero_subtitle_font')->nullable();
            }

            $columns = [
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
            ];

            foreach ($columns as $col) {
                if (!Schema::hasColumn('home_settings', $col)) {
                    $table->string($col)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('home_settings')) {
            return;
        }
        Schema::table('home_settings', function (Blueprint $table) {
            $cols = [
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
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('home_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
