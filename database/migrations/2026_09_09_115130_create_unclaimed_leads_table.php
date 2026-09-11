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
        Schema::create('unclaimed_leads', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->enum('lead_type', ['service', 'product'])->default('service');
            $table->text('description')->nullable();
            $table->foreignId('service_category_id')->nullable();
            $table->string('category_text')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('suggested_by')->nullable();
            $table->enum('status', ['unclaimed', 'claim_pending', 'verified'])->default('unclaimed');
            $table->boolean('approved')->default(false)->after('status');
            $table->foreignId('claimed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unclaimed_leads');
    }
};
