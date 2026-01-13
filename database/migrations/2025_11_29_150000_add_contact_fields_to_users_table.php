<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->string('country')->nullable()->after('phone_number');
            $table->string('town')->nullable()->after('country');
            $table->string('zipcode')->nullable()->after('town');
            $table->string('address')->nullable()->after('zipcode');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number','country','town','zipcode','address']);
        });
    }
};
