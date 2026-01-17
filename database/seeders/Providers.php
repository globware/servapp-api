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

class Providers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $userServiceService = new UserServiceService;
        // $userServices = $userServiceService->getServices();
        // foreach($userServices as $userService) {
        //     $phoneNumbers = $userService->phone_numbers;
        //     if(!empty($phoneNumbers)) {
        //         foreach($phoneNumbers as $key=>$phoneNumber) {
        //             if($phoneNumber == " ") unset($phoneNumbers[$key]);
        //         }
        //     }
        //     $userService->phone_numbers = $phoneNumbers;
        //     $userService->update();
        // }

        $providers = [
            [
                "firstname" => "David", "surname" => "Joe"
            ],
            [
                "firstname" => "Elvis", "surname" => "Akao"
            ],
            [
                "firstname" => "Rufai", "surname" => "Jalingo"
            ],
            [
                "firstname" => "Ibrahim", "surname" => "Buhari"
            ],
            [
                "firstname" => "Murtala", "surname" => "Danjuma"
            ],
            [
                "firstname" => "Segun", "surname" => "Obasanjo"
            ],
            [
                "firstname" => "Mariam", "surname" => "Adeleke"
            ],
            [
                "firstname" => "Fetuga", "surname" => "Ademuni"
            ],
            [
                "firstname" => "Nasir", "surname" => "Mohammed"
            ],
            [
                "firstname" => "Ebere", "surname" => "Okonji"
            ],
            [
                "firstname" => "Yahaya", "surname" => "Kingibe"
            ],
            [
                "firstname" => "Shola", "surname" => "Ojo"
            ],
            [
                "firstname" => "Ferdinand", "surname" => "Wende"
            ],
            [
                "firstname" => "Danjuma", "surname" => "Gowon"
            ],
            [
                "firstname" => "Moses", "surname" => "Bakari"
            ],
            [
                "firstname" => "Chinedu", "surname" => "Iloma"
            ],
            [
                "firstname" => "Joe", "surname" => "Igbokwe"
            ],
            [
                "firstname" => "Bala", "surname" => "Dankasa"
            ],
            [
                "firstname" => "Isaiah", "surname" => "Abdulaziz"
            ],
            [
                "firstname" => "Cynthia", "surname" => "Ajai"
            ],
            [
                "firstname" => "Charles", "surname" => "Effiong"
            ],
            [
                "firstname" => "Markus", "surname" => "Oghaeme"
            ],
            [
                "firstname" => "Josphine", "surname" => "Osai"
            ],
            [
                "firstname" => "Mark", "surname" => "Williams"
            ],
            [
                "firstname" => "Kunle", "surname" => "Olawosin"
            ],
            [
                "firstname" => "Seun", "surname" => "Olubunde"
            ],
            [
                "firstname" => "Tunde", "surname" => "Bakari"
            ],
            [
                "firstname" => "Mohammed", "surname" => "Ahmed"
            ],
            [
                "firstname" => "Gozie", "surname" => "Okafor"
            ],
            [
                "firstname" => "Nnamdi", "surname" => "Emuche"
            ],
        ];

        foreach($providers as $provider) {
            DB::beginTransaction();
            try{
                $emailService = new EmailService;
                $userService = new UserService;
                $userServiceService = new UserServiceService;

                $provider['email'] = strtolower($provider['firstname'])."@gmail.com";

                $emailService->saveEmailVerificationToken($provider['email']);
                $emailVerification = $emailService->emailExists($provider['email']);
                $emailVerification->verified = true;
                $emailVerification->update();

                $provider['password'] = $provider['firstname']."_123";
                $provider['hasService'] = true;

                $prefixes = ["080", "081", "070", "090"];
                $provider['phoneNumber'] = Helpers::generatePhoneNumber();

                $user = $userService->save($provider);

                $services = $this->generateRandomServices();

                foreach($services as $service) {
                    $service['userId'] = $user->id;
                    $userServiceService->save($service);
                }
                DB::commit();
            } catch(\Exception $e) {
                DB::rollBack();
                Utilities::error($e, "error");
                throw $e;
            }
        }
    }


    function generateRandomServices()
    {
        $serviceService = new ServiceService;
        $locationService = new LocationService;

        $serviceCount = rand(1, 4);
        $services = [];

        for ($i = 0; $i < $serviceCount; $i++) {

            // --- FIXED SERVICE LIST (IDs 1–22) ---
            // Your DB should follow this mapping.
            $serviceMapping = [
                1 => "Plumbing",
                2 => "Welding",
                3 => "Vulcanizer",
                4 => "Carpentry",
                5 => "Furniture",
                6 => "Interior Decoration",
                7 => "Mechanic",
                8 => "Car Electrician",
                9 => "Electrician",
                10 => "Nursing",
                11 => "Medical Services",
                12 => "Dentistry",
                13 => "Cleaner",
                14 => "Pharmaceutical Services",
                15 => "Nanny",
                16 => "Child Care",
                17 => "Laptop Repairer",
                18 => "Phone Repairer",
                19 => "Electronics Repairer",
                20 => "Tailoring",
                21 => "Event Planning",
                22 => "Panel Beater"
            ];

            // --- SERVICE ID ---
            $serviceId = rand(1, 22);

            // Fetch service from database
            $service = $serviceService->getService($serviceId);

            // Fallback if DB not found
            $serviceName = $service->name ?? $serviceMapping[$serviceId];

            // --- COMPANY NAME BASED ON SERVICE ---
            $serviceNamePrefixes = [
                "Elite", "Prime", "Swift", "Metro", "Galaxy", "Pro", "Master", "Topline", "Rapid", "Trust"
            ];

            $name = $serviceNamePrefixes[array_rand($serviceNamePrefixes)] . " " . $serviceName;

            // --- ADDRESS ---
            $streetNames = ["Allen Avenue", "Lekki Phase 1", "Ikeja Road", "Gbagada Street", "Herbert Macaulay Way", "Broad Street"];
            $address = rand(1, 200) . " " . $streetNames[array_rand($streetNames)];

            // --- LOCATION ID ---
            $locationId = rand(1, 8809);
            $location = $locationService->getLocation($locationId);

            // --- EMAIL ---
            $emailDomain = ["gmail.com", "yahoo.com", "outlook.com"];
            $email = Str::slug($name) . rand(10, 999) . "@" . $emailDomain[array_rand($emailDomain)];

            // --- PHONE NUMBER ---
            $prefixes = ["080", "081", "070", "090"];
            $phoneCount = rand(1, 3);
            $phoneNumbers = "";
            for ($i = 0; $i < $phoneCount; $i++) {
                $phoneNumbers .= Helpers::generatePhoneNumber().", ";
            }

            // --- LONGITUDE & LATITUDE ---
            [$long, $lat] = Helpers::getCoordinatesWithinLocation($location->latitude, $location->longitude);

            // --- MIN PRICE ---
            $minPrice = rand(1, 100) <= 20 ? null : rand(100, 100000);

            // --- MAX PRICE ---
            if ($minPrice === null || rand(1, 100) <= 80) {
                $maxPrice = null;
            } else {
                $maxPrice = rand($minPrice, $minPrice + rand(1000, 50000));
            }

            // --- ALL DAY ---
            $allDay = (bool) rand(0, 1);

            // --- TIMES ---
            if ($allDay) {
                $openingTime = null;
                $closingTime = null;
            } else {
                $openingTime = sprintf("%02d:00 AM", rand(7, 10));
                $closingTime = sprintf("%02d:00 PM", rand(3, 10));
            }

            // --- DESCRIPTION ---
            $description = rand(1, 100) <= 50
                ? "Professional and reliable {$serviceName} service with customer-focused delivery."
                : null;

            // --- TAGS ---
            $tagsForServices = [
                "Plumbing" => ["plumber", "pipes", "water repair", "leak fix"],
                "Welding" => ["metalwork", "fabrication", "welding service"],
                "Vulcanizer" => ["tire repair", "vulcanizing", "tyre service"],
                "Carpentry" => ["woodwork", "carpenter", "furniture"],
                "Furniture" => ["furniture maker", "wood design"],
                "Interior Decoration" => ["interior design", "decor", "home styling"],
                "Mechanic" => ["auto repair", "mechanic", "car service"],
                "Car Electrician" => ["auto electrical", "car wiring"],
                "Electrician" => ["electrical repair", "wiring"],
                "Nursing" => ["nurse", "healthcare"],
                "Medical Services" => ["clinic", "healthcare"],
                "Dentistry" => ["dentist", "oral care"],
                "Cleaner" => ["cleaning service", "janitorial"],
                "Pharmaceutical Services" => ["pharmacy", "drugs"],
                "Nanny" => ["nanny", "child care"],
                "Child Care" => ["child care", "babysitting"],
                "Laptop Repairer" => ["laptop repair", "IT service"],
                "Phone Repairer" => ["phone repair", "mobile service"],
                "Electronics Repairer" => ["electronics repair", "technician"],
                "Tailoring" => ["tailor", "fashion designer"],
                "Event Planning" => ["event planner", "event management"],
                "Panel Beater" => ["bodywork", "panel beating"]
            ];

            $allTags = $tagsForServices[$serviceName] ?? ["service"];
            shuffle($allTags);
            $tags = array_slice($allTags, 0, rand(2, min(4, count($allTags))));

            // --- MEDIA IDs ---
            $mediaIds = [];
            for ($m = 0; $m < rand(2, 4); $m++) {
                $mediaIds[] = rand(7, 79);
            }

            // --- BUILD SERVICE PAYLOAD ---
            $services[] = [
                "name" => $name,
                "serviceId" => $serviceId,
                "address" => $address,
                "locationId" => $locationId,
                "stateId" => $location->state->id,
                "countryId" => $location->state->country->id,
                "email" => $email,
                "phoneNumbers" => $phoneNumbers,
                "long" => $long,
                "lat" => $lat,
                "minPrice" => $minPrice,
                "maxPrice" => $maxPrice,
                "allDay" => $allDay,
                "openingTime" => $openingTime,
                "closingTime" => $closingTime,
                "description" => $description,
                "tags" => $tags,
                "mediaIds" => $mediaIds
            ];
        }

        return $services;
    }
}
