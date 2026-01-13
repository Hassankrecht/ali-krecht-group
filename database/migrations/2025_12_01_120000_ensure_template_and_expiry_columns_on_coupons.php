<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'template_id')) {
                $table->unsignedBigInteger('template_id')->nullable()->after('user_id');
                $table->foreign('template_id')->references('id')->on('coupons')->nullOnDelete();
            }
            if (!Schema::hasColumn('coupons', 'expiry_days')) {
                $table->integer('expiry_days')->nullable()->after('expiration_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'template_id')) {
                $table->dropForeign(['template_id']);
                $table->dropColumn('template_id');
            }
            if (Schema::hasColumn('coupons', 'expiry_days')) {
                $table->dropColumn('expiry_days');
            }
        });
    }
};
