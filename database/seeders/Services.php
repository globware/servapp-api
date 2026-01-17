<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Service;

class Services extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            "Plumbing", "Welding", "Vulcanizer", "Carpentry", "Furniture", "Welding", "Interior Decoration", "Mechanic", "Car Electrician", "Electrician",
            "Nursing", "Medical Services", "Dentistry", "Cleaner", "Pharmaceutical Services", "Nanny", "Child Care", "Laptop Repairer", "Phone Repairer", 
            "Electronics Repairer", "Tailoring", "Event Planning", "Panel Beater"
        ];

        foreach($services as $service) Service::firstOrCreate(["name" => $service]);
    }
}
