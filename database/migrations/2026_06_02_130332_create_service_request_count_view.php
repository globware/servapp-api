<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates two read-only views that aggregate UserServiceRequest counts
     * broken down by status (pending / engaged / completed / cancelled).
     *
     * View 1 — vw_user_service_request_counts
     *   Granularity : one row per user_service
     *   Usage       : UserService::join('vw_user_service_request_counts', ...)
     *
     * View 2 — vw_user_service_request_counts_by_user
     *   Granularity : one row per user (owner of one-or-more user_services)
     *   Usage       : User::join('vw_user_service_request_counts_by_user', ...)
     *
     * Note: the "Status" column in user_service_requests is quoted because
     * it was created with a capital S. LOWER() normalises any casing in stored
     * values so comparisons are case-insensitive.
     */
    public function up(): void
    {
        // ----------------------------------------------------------------
        // View 1 — per user_service
        // ----------------------------------------------------------------
        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW vw_user_service_request_counts AS
            SELECT
                usr.user_service_id,
                COUNT(*)                                                         AS total_requests,
                COUNT(*) FILTER (WHERE LOWER(usr."status") = 'pending')          AS pending_count,
                COUNT(*) FILTER (WHERE LOWER(usr."status") = 'engaged')          AS engaged_count,
                COUNT(*) FILTER (WHERE LOWER(usr."status") = 'completed')        AS completed_count,
                COUNT(*) FILTER (WHERE LOWER(usr."status") = 'cancelled')        AS cancelled_count
            FROM user_service_requests usr
            GROUP BY usr.user_service_id
        SQL);
 
        // ----------------------------------------------------------------
        // View 2 — per owner user (across all their user_services)
        // ----------------------------------------------------------------
        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW vw_user_service_request_counts_by_user AS
            SELECT
                us.user_id                                                           AS owner_user_id,
                COUNT(DISTINCT us.id)                                                AS total_services,
                COUNT(usr.id)                                                        AS total_requests,
                COUNT(usr.id) FILTER (WHERE LOWER(usr."status") = 'pending')         AS pending_count,
                COUNT(usr.id) FILTER (WHERE LOWER(usr."status") = 'engaged')         AS engaged_count,
                COUNT(usr.id) FILTER (WHERE LOWER(usr."status") = 'completed')       AS completed_count,
                COUNT(usr.id) FILTER (WHERE LOWER(usr."status") = 'cancelled')       AS cancelled_count
            FROM user_services us
            LEFT JOIN user_service_requests usr ON usr.user_service_id = us.id
            GROUP BY us.user_id
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_user_service_request_counts');
        DB::statement('DROP VIEW IF EXISTS vw_user_service_request_counts_by_user');
    }
};
