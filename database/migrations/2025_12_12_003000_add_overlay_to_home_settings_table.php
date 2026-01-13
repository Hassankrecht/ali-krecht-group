<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->string('overlay_color')->nullable()->after('hero_bg_size');
            $table->integer('overlay_opacity')->nullable()->after('overlay_color'); // 0-100
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn(['overlay_color', 'overlay_opacity']);
        });
    }
};
