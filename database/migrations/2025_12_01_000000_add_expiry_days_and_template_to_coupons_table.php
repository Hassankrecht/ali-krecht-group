<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('template_id')->nullable()->after('user_id');
            $table->integer('expiry_days')->nullable()->after('expiration_date');
            $table->foreign('template_id')
                ->references('id')
                ->on('coupons')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn(['template_id', 'expiry_days']);
        });
    }
};
