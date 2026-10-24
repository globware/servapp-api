<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('unclaimed_leads', function (Blueprint $table) {
            if (!Schema::hasColumn('unclaimed_leads', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
        });

        DB::statement("UPDATE unclaimed_leads SET title = business_name WHERE title IS NULL AND business_name IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unclaimed_leads', function (Blueprint $table) {
            if (Schema::hasColumn('unclaimed_leads', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
