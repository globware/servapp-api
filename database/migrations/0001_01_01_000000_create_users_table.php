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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('firstname');
                $table->string('surname');
                $table->string('email')->unique();
                $table->string("phone_number");
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->decimal('provider_id', 65, 0)->nullable();
                $table->foreignId("photo_id")->nullable();
                $table->foreignId("location_id")->nullable();
                $table->foreignId("state_id")->nullable();
                $table->foreignId("country_id")->nullable();
                $table->integer("tokens")->default(0);
                $table->foreignId("identification_id")->nullable();
                $table->string("identification_no")->nullable();
                $table->boolean("has_service")->default(false);
                $table->string("referral_code");
                $table->foreignId("referred_by")->nullable();
                $table->boolean("suspended")->default(false);
                $table->dateTime("last_login")->nullable();
                $table->string("refresh_token")->nullable();
                $table->string("refresh_token_device")->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('email', 191);
                $table->string('token_signature');
                $table->timestamp('expires_at');
                $table->boolean("verified")->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
