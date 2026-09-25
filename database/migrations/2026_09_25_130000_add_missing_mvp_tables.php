<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Quotes
        if (!Schema::hasTable('quotes')) {
            Schema::create('quotes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_service_request_id')->constrained('user_service_requests')->cascadeOnDelete();
                $table->integer('amount');
                $table->string('currency')->default('NGN');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Chat Media (Pivot)
        if (!Schema::hasTable('chat_media')) {
            Schema::create('chat_media', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chat_id')->constrained('chats')->cascadeOnDelete();
                $table->foreignId('file_id')->constrained('files')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 3. User Product Reviews
        if (!Schema::hasTable('user_product_reviews')) {
            Schema::create('user_product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_product_id')->constrained('user_products')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->integer('rating');
                $table->text('review')->nullable();
                $table->timestamps();
            });
        }

        // 4. User Blocks
        if (!Schema::hasTable('user_blocks')) {
            Schema::create('user_blocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                
                $table->unique(['user_id', 'blocked_user_id']);
            });
        }

        // 5. Provider Verifications
        if (!Schema::hasTable('provider_verifications')) {
            Schema::create('provider_verifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();
                $table->enum('status', ['pending', 'approved', 'rejected', 'needs_info'])->default('pending');
                $table->text('reason')->nullable();
                $table->foreignId('id_file_id')->constrained('files')->cascadeOnDelete();
                $table->foreignId('business_file_id')->nullable()->constrained('files')->nullOnDelete();
                $table->timestamp('decided_at')->nullable();
                $table->timestamps();
            });
        }

        // 6. Update Chats Table
        Schema::table('chats', function (Blueprint $table) {
            if (!Schema::hasColumn('chats', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            if (Schema::hasColumn('chats', 'latitude')) {
                $table->dropColumn(['latitude', 'longitude']);
            }
        });
        Schema::dropIfExists('provider_verifications');
        Schema::dropIfExists('user_blocks');
        Schema::dropIfExists('user_product_reviews');
        Schema::dropIfExists('chat_media');
        Schema::dropIfExists('quotes');
    }
};
