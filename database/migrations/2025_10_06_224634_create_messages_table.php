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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId("sender_id")->nullable();
            $table->string("sender_type")->nullable();
            $table->foreignId("receiver_id")->nullable();
            $table->string("receiver_type")->nullable();
            $table->text("message");
            $table->boolean("read")->default(false);
            $table->boolean("sender_archived")->default("false");
            $table->boolean("sender_hidden")->default("false");
            $table->boolean("receiver_archived")->default("false");
            $table->boolean("receiver_hidden")->default("false");
            $table->boolean("is_orphaned")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
