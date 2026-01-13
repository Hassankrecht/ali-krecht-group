<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->integer('usage_limit')->default(1)->after('value');
            $table->integer('used_count')->default(0)->after('usage_limit');
            $table->decimal('min_total', 10, 2)->nullable()->after('used_count');
            $table->string('generated_for')->nullable()->after('min_total'); // welcome, refund, etc.
            $table->dateTime('starts_at')->nullable()->after('generated_for');
            $table->index(['user_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'code']);
            $table->dropColumn(['user_id','usage_limit','used_count','min_total','generated_for','starts_at']);
        });
    }
};
