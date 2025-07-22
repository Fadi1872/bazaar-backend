<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Restaurants'],
            ['name' => 'Clothing'],
            ['name' => 'Electronics'],
            ['name' => 'Books'],
            ['name' => 'Groceries'],
            ['name' => 'Health & Beauty'],
            ['name' => 'Home & Living'],
        ];

        DB::table('store_categories')->insert($categories);
    }
}
