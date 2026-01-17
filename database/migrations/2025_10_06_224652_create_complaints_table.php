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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->morphs("target");
            $table->foreignId("reference_id")->nullable();
            $table->string("reference_type")->nullable();
            $table->string("title");
            $table->text("content");
            $table->boolean("closed")->default(false);
            $table->foreignId("closed_by")->nullable();
            $table->boolean("is_orphaned")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
