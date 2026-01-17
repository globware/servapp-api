<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\NigeriaStatesList;

use App\Models\Country;
use App\Models\State;
use App\Models\Location;

use App\Utilities;

class States extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nigeria = Country::where("name", "Nigeria")->first();
        $statesList = new NigeriaStatesList;
        $states = $statesList->states;
          
        foreach($states as $state) {
            $stateObj = State::firstOrCreate(["name" => $state['state'], "country_id" => $nigeria->id]);
            foreach($state['lgas'] as $lga) {
                foreach($lga['wards'] as $location) {
                    Location::firstOrCreate(["state_id" => $stateObj->id, "name" => $location['name'], "longitude" => $location['longitude'], "latitude" => $location['latitude']]);
                    Utilities::logStuff($location['name']);
                }
                Utilities::logStuff($lga['name']);
            }
            Utilities::logStuff($state['state']);
        }
        // dd('here1');
    }
}
