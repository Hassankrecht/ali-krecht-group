<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            if (!Schema::hasColumn('checkouts', 'source_platform')) {
                $table->string('source_platform', 20)->default('web')->after('status');
            }

            if (!Schema::hasColumn('checkouts', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('source_platform');
            }

            if (!Schema::hasColumn('checkouts', 'order_note')) {
                $table->text('order_note')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('checkouts', 'delivery_fee')) {
                $table->decimal('delivery_fee', 10, 2)->default(0)->after('discount_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $columns = [];

            foreach (['source_platform', 'payment_method', 'order_note', 'delivery_fee'] as $column) {
                if (Schema::hasColumn('checkouts', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
