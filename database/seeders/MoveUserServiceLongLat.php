<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\UserService;

class MoveUserServiceLongLat extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = UserService::all();
        if($services->count() > 0) {
            foreach($services as $service) {
                $service->long = $service->longitude;
                $service->lat = $service->latitude;
                $service->update();
            }
        }
    }
}
