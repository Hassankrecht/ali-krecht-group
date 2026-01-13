<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->string('btn_primary_text')->nullable();
            $table->string('btn_primary_link')->nullable();
            $table->string('btn_primary_color')->nullable();
            $table->string('btn_secondary_text')->nullable();
            $table->string('btn_secondary_link')->nullable();
            $table->string('btn_secondary_color')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn([
                'btn_primary_text',
                'btn_primary_link',
                'btn_primary_color',
                'btn_secondary_text',
                'btn_secondary_link',
                'btn_secondary_color',
            ]);
        });
    }
};
