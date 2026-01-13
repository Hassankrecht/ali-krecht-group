<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->integer('hero_bg_pos_x')->nullable()->after('hero_bg_size'); // %
            $table->integer('hero_bg_pos_y')->nullable()->after('hero_bg_pos_x'); // %
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_bg_pos_x', 'hero_bg_pos_y']);
        });
    }
};
