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
        Schema::create('user_product_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_product_id");
            $table->foreignId("user_id")->nullable();
            $table->text("message");
            $table->boolean("seen")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_product_messages');
    }
};
