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
            if (!Schema::hasColumn('home_settings', 'hero_height')) {
                $table->integer('hero_height')->nullable(); // base column if missing
            }
            if (!Schema::hasColumn('home_settings', 'hero_width')) {
                $table->integer('hero_width')->nullable()->after('hero_height'); // px
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('home_settings')) {
            return;
        }
        Schema::table('home_settings', function (Blueprint $table) {
            if (Schema::hasColumn('home_settings', 'hero_width')) {
                $table->dropColumn('hero_width');
            }
        });
    }
};
