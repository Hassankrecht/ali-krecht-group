<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود الكوبون
            $table->enum('type', ['percent', 'fixed']); // نوع الخصم
            $table->decimal('value', 10, 2); // قيمة الخصم
            $table->dateTime('expiration_date')->nullable(); // تاريخ انتهاء
            $table->boolean('status')->default(true); // فعال / غير فعال
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
