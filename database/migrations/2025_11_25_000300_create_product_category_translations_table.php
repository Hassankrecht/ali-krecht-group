<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table already exists from a later corrected migration, so skip if present.
        if (Schema::hasTable('product_category_translations')) {
            return;
        }

        Schema::create('product_category_translations', function (Blueprint $table) {
            $table->id();
            // Using category_id to align with the corrected schema
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();
            $table->unique(['category_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_category_translations');
    }
};
