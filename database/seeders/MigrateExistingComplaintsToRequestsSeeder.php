<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateExistingComplaintsToRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Migrate existing complaints to point to requests where possible
        $complaints = DB::table('complaints')->get();

        foreach ($complaints as $complaint) {
            if ($complaint->target_type === 'App\Models\UserService') {
                // Client complained about a service
                $request = DB::table('user_service_requests')
                    ->where('user_id', $complaint->user_id)
                    ->where('user_service_id', $complaint->target_id)
                    ->first();

                if ($request) {
                    DB::table('complaints')
                        ->where('id', $complaint->id)
                        ->update([
                            'target_type' => 'App\Models\UserServiceRequest',
                            'target_id' => $request->id,
                            'reference_type' => 'App\Models\UserService',
                            'reference_id' => $request->user_service_id,
                        ]);
                }
            } elseif ($complaint->target_type === 'App\Models\User') {
                // Provider complained about a client ($complaint->target_id)
                $request = DB::table('user_service_requests')
                    ->join('user_services', 'user_services.id', '=', 'user_service_requests.user_service_id')
                    ->where('user_service_requests.user_id', $complaint->target_id)
                    ->where('user_services.user_id', $complaint->user_id)
                    ->select('user_service_requests.id', 'user_service_requests.user_service_id')
                    ->first();

                if ($request) {
                    DB::table('complaints')
                        ->where('id', $complaint->id)
                        ->update([
                            'target_type' => 'App\Models\UserServiceRequest',
                            'target_id' => $request->id,
                            'reference_type' => 'App\Models\UserService',
                            'reference_id' => $request->user_service_id,
                        ]);
                }
            }
        }
    }
}
