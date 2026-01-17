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
        Schema::create('user_service_update_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_service_id");
            $table->json("update");
            $table->boolean("approved")->nullable();
            $table->foreignId("treated_by")->nullable();
            $table->text("rejected_reason")->nullable();
            $table->text("previous_rejection_reason")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_service_update_logs');
    }
};
