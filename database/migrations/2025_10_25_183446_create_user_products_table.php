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
        Schema::create('user_products', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId("user_id");
            $table->foreignId("product_id");
            $table->tinyInteger("ratings")->nullable();
            $table->foreignId("country_id")->nullable();
            $table->foreignId("state_id")->nullable();
            $table->foreignId("location_id")->nullable();
            $table->boolean("verified")->default(false);
            $table->boolean("suspended")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_products');
    }
};
