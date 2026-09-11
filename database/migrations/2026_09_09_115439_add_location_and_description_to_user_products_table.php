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
        Schema::table('user_products', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropColumn(['description', 'address', 'latitude', 'longitude', 'price', 'active']);
        });
    }
};
