<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_services', function (Blueprint $table) {
            $table->foreignId('provider_location_id')
                ->nullable()
                ->after('user_id')
                ->constrained('provider_locations')
                ->nullOnDelete();
        });

        Schema::table('user_products', function (Blueprint $table) {
            $table->foreignId('provider_location_id')
                ->nullable()
                ->after('user_id')
                ->constrained('provider_locations')
                ->nullOnDelete();
        });

        // Data Backfill for Existing Services
        $services = DB::table('user_services')
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->get();

        foreach ($services as $service) {
            $existing = DB::table('provider_locations')
                ->where('user_id', $service->user_id)
                ->where('address', $service->address)
                ->first();

            if (!$existing) {
                $hasDefault = DB::table('provider_locations')
                    ->where('user_id', $service->user_id)
                    ->where('is_default', true)
                    ->exists();

                $title = trim(explode(',', $service->address)[0]);
                if (empty($title)) {
                    $title = 'Primary Location';
                }

                $locationId = DB::table('provider_locations')->insertGetId([
                    'user_id' => $service->user_id,
                    'title' => Str::limit($title, 100, ''),
                    'address' => $service->address,
                    'latitude' => $service->latitude,
                    'longitude' => $service->longitude,
                    'country_id' => $service->country_id,
                    'state_id' => $service->state_id,
                    'location_id' => $service->location_id,
                    'is_default' => !$hasDefault,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $locationId = $existing->id;
            }

            DB::table('user_services')
                ->where('id', $service->id)
                ->update(['provider_location_id' => $locationId]);
        }

        // Data Backfill for Existing Products
        $products = DB::table('user_products')
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->get();

        foreach ($products as $product) {
            $existing = DB::table('provider_locations')
                ->where('user_id', $product->user_id)
                ->where('address', $product->address)
                ->first();

            if (!$existing) {
                $hasDefault = DB::table('provider_locations')
                    ->where('user_id', $product->user_id)
                    ->where('is_default', true)
                    ->exists();

                $title = trim(explode(',', $product->address)[0]);
                if (empty($title)) {
                    $title = 'Primary Location';
                }

                $locationId = DB::table('provider_locations')->insertGetId([
                    'user_id' => $product->user_id,
                    'title' => Str::limit($title, 100, ''),
                    'address' => $product->address,
                    'latitude' => $product->latitude,
                    'longitude' => $product->longitude,
                    'country_id' => $product->country_id ?? null,
                    'state_id' => $product->state_id ?? null,
                    'location_id' => $product->location_id ?? null,
                    'is_default' => !$hasDefault,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $locationId = $existing->id;
            }

            DB::table('user_products')
                ->where('id', $product->id)
                ->update(['provider_location_id' => $locationId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_services', function (Blueprint $table) {
            $table->dropForeign(['provider_location_id']);
            $table->dropColumn('provider_location_id');
        });

        Schema::table('user_products', function (Blueprint $table) {
            $table->dropForeign(['provider_location_id']);
            $table->dropColumn('provider_location_id');
        });
    }
};
