<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserServiceRequest;

class ChatsBackfill extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chats')
            ->whereNull('requestable_id')
            ->whereNotNull('user_service_request_id')
            ->update([
                'requestable_id' => DB::raw('user_service_request_id'),
                'requestable_type' => UserServiceRequest::$type,
            ]);
    }
}
