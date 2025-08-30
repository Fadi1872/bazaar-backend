<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Smartphones',
            'Laptops',
            'Tablets',
            'Smartwatches',
            'Home Appliances',
            'Furniture',
            'Clothing',
            'Shoes',
            'Books',
            'Beauty & Personal Care',
            'Sports & Outdoors',
            'Toys & Games',
            'Groceries',
            'Automotive',
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(['name' => $category]);
        }
    }
}
