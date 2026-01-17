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
        Schema::create('user_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_service_id");
            $table->foreignId("user_id");
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
        Schema::dropIfExists('user_service_requests');
    }
};
