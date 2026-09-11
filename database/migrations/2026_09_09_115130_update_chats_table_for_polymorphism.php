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
        Schema::table('chats', function (Blueprint $table) {
            $table->nullableMorphs('requestable'); // adds requestable_id and requestable_type
            $table->foreignId('user_service_request_id')->nullable()->change();
        });
        
        DB::statement("UPDATE chats SET requestable_id = user_service_request_id, requestable_type = 'App\\\\Models\\\\UserServiceRequest' WHERE user_service_request_id IS NOT NULL");
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropMorphs('requestable');
            $table->foreignId('user_service_request_id')->nullable(false)->change();
        });
    }
};
