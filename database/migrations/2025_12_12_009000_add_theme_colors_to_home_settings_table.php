<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('home_settings', 'primary_color')) {
                $table->string('primary_color')->nullable()->after('font_family');
            }
            if (!Schema::hasColumn('home_settings', 'secondary_color')) {
                $table->string('secondary_color')->nullable()->after('primary_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            if (Schema::hasColumn('home_settings', 'primary_color')) {
                $table->dropColumn('primary_color');
            }
            if (Schema::hasColumn('home_settings', 'secondary_color')) {
                $table->dropColumn('secondary_color');
            }
        });
    }
};
