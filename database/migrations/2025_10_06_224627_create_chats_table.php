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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_service_request_id");
            $table->foreignId("sender_id");
            $table->string("sender_type")->nullable();
            $table->foreignId("receiver_id");
            $table->string("receiver_type")->nullable();
            $table->text("message");
            $table->boolean("seen")->default(false);
            $table->boolean("is_orphaned")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
