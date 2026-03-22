<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\Services;
use Database\Seeders\MoveLocationLongLat;
use Database\Seeders\AdminSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $seeders = [
            new Services,
            new AdminSeeder
            // new MoveLocationLongLat
        ];

        foreach($seeders as $seeder) $seeder->run();
    }
}
