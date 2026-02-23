<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Location;

class MoveLocationLongLat extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::all();
        if($locations->count() > 0) {
            foreach($locations as $location) {
                $location->long = $location->longitude;
                $location->lat = $location->latitude;
                $location->update();
            }
        }
    }
}
