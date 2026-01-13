<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->boolean('btn_primary_visible')->default(true)->after('btn_primary_color');
            $table->boolean('btn_secondary_visible')->default(true)->after('btn_secondary_color');
            $table->string('btn_primary_style')->nullable()->after('btn_primary_visible'); // solid|outline|pill
            $table->string('btn_secondary_style')->nullable()->after('btn_secondary_visible');
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn([
                'btn_primary_visible',
                'btn_secondary_visible',
                'btn_primary_style',
                'btn_secondary_style',
            ]);
        });
    }
};
