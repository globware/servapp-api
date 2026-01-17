<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Admin::count() === 0) {
            Admin::create([
                'firstname' => 'Super',
                'surname' => 'Admin',
                'email' => 'admin@serveapp.com',
                'password' => Hash::make('password'),
            ]);
            $this->command->info('Admin user created: admin@serveapp.com / password');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
