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
        Schema::create('user_services', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId("user_id");
            $table->foreignId("service_id");
            $table->foreignId("cover_photo_id")->nullable();
            $table->json("phone_numbers")->nullable();
            $table->string("email")->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean("all_day")->default(false);
            $table->decimal("min_price")->nullable();
            $table->decimal("max_price")->nullable();
            $table->tinyInteger("ratings")->nullable();
            $table->foreignId("country_id")->nullable();
            $table->foreignId("state_id")->nullable();
            $table->foreignId("location_id")->nullable();
            $table->string("address")->nullable();
            $table->string("longitude")->nullable();
            $table->string("latitude")->nullable();
            $table->text("description")->nullable();
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
        Schema::dropIfExists('user_services');
    }
};
