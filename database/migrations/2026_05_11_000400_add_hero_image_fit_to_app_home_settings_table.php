<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('app_home_settings') || Schema::hasColumn('app_home_settings', 'hero_image_fit')) {
            return;
        }

        Schema::table('app_home_settings', function (Blueprint $table) {
            $table->string('hero_image_fit', 20)->default('contain')->after('hero_image_path');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('app_home_settings') || !Schema::hasColumn('app_home_settings', 'hero_image_fit')) {
            return;
        }

        Schema::table('app_home_settings', function (Blueprint $table) {
            $table->dropColumn('hero_image_fit');
        });
    }
};