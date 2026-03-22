<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'firstname' => 'Super',
                'surname' => 'Admin',
                'email' => 'admin@serveapp.com',
            ]
        ];

        foreach($admins as $adminData) {
            $admin = Admin::where("email", $adminData['email'])->first();
            if(!$admin) {
                $admin = new Admin;
                $admin->firstname = $adminData['firstname'];
                $admin->surname = $adminData['surname'];
                $admin->email = $adminData['email'];
                $admin->password = 'password';
                $admin->save();
            }
        }
        // if (Admin::count() === 0) {
        //     Admin::create([
        //         'firstname' => 'Super',
        //         'surname' => 'Admin',
        //         'email' => 'admin@serveapp.com',
        //         'password' => Hash::make('password'),
        //     ]);
        //     $this->command->info('Admin user created: admin@serveapp.com / password');
        // } else {
        //     $this->command->info('Admin user already exists.');
        // }
    }
}
