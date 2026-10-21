<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;

class Products extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            "Electronics & Gadgets",
            "Phones & Tablets",
            "Computers & Accessories",
            "Home Appliances",
            "Furniture & Home Decor",
            "Fashion & Clothing",
            "Groceries & Foodstuffs",
            "Health & Beauty",
            "Automotive Parts",
            "Building & Construction Materials"
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(["name" => $product]);
        }
    }
}
