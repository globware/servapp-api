<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Services\EmailService;
use App\Services\UserService;
use App\Services\LocationService;
use App\Services\ServiceService;
use App\Services\UserServiceService;

use App\Utilities;
use App\Helpers;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = [
            // [
            //     "firstname" => "Magnus",
            //     "surname" => "Abe",
            //     "email" => "magnus@gmail.com",
            //     "phoneNumber" => "080994532987",
            // ],
            // [
            //     "firstname" => "Charity",
            //     "surname" => "Oke",
            //     "email" => "charity@gmail.com",
            //     "phoneNumber" => "080994532987",
            //     "hasService" => true
            // ]
            [
                "firstname" => "Emmanuel", "surname" => "Ango"
            ],
            [
                "firstname" => "Judith", "surname" => "Agbo"
            ],
            [
                "firstname" => "Sadiq", "surname" => "Abubakar"
            ],
            [
                "firstname" => "Cyril", "surname" => "Olubede"
            ],
            [
                "firstname" => "Ugochukwu", "surname" => "Oranya"
            ],
        ];
        /*
            $table->string('firstname');
            $table->string('surname');
            $table->string('email')->unique();
            $table->string("phone_number");
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId("photo_id")->nullable();
            $table->integer("tokens")->default(0);
            $table->foreignId("identification_id")->nullable();
            $table->string("identification_no")->nullable();
            $table->boolean("has_service")->default(false);
        */

        foreach($users as $user) {
            DB::beginTransaction();
            try{
                $emailService = new EmailService;
                $userService = new UserService;
                $locationService = new LocationService;

                $user['email'] = strtolower($user['firstname'])."@gmail.com";

                $emailService->saveEmailVerificationToken($user['email']);
                $emailVerification = $emailService->emailExists($user['email']);
                $emailVerification->verified = true;
                $locationId = rand(1, 8809);
                $location = $locationService->getLocation($locationId);
                $user['locationId'] = $location->id;
                $user['stateId'] = $location->state->id;
                $user['countryId'] = $location->state->country->id;
                $emailVerification->update();

                $user['password'] = $user['firstname']."_123";

                $user['phoneNumber'] = Helpers::generatePhoneNumber();

                $user = $userService->save($user);

                DB::commit();
            } catch(\Exception $e) {
                DB::rollBack();
                Utilities::error($e, "error");
                throw $e;
            }
        }
    }
}
