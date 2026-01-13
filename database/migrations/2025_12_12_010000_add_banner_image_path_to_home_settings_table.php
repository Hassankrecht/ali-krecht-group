<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('home_settings', 'banner_image_path')) {
                $table->string('banner_image_path')->nullable()->after('banner_link');
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            if (Schema::hasColumn('home_settings', 'banner_image_path')) {
                $table->dropColumn('banner_image_path');
            }
        });
    }
};
