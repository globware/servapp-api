<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\Services;
use Database\Seeders\MoveLocationLongLat;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            Services::class,
            AdminSeeder::class,
            Users::class,
            Providers::class,
            MoveLocationLongLat::class,
        ]);
    }
}
