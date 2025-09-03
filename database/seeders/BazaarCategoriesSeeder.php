<?php

namespace Database\Seeders;

use App\Models\BazaarCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BazaarCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Fashion'],
            ['name' => 'Home & Kitchen'],
            ['name' => 'Beauty & Personal Care'],
            ['name' => 'Sports & Outdoors'],
            ['name' => 'Books & Stationery'],
            ['name' => 'Toys & Games'],
            ['name' => 'Health & Wellness'],
            ['name' => 'Automotive'],
            ['name' => 'Groceries'],
        ];

        foreach ($categories as $category) {
            BazaarCategory::firstOrCreate(['name' => $category['name']]);
        }
    }
}
