<?php

namespace Database\Seeders;

use app\Contracts\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            "name" => "fadiN",
            "email" => "fadinoumih18@gmail.com",
            "password" => Hash::make("12345678"),
            "age" => 23,
            "gender" => 1,
            "number" => "0935741791"
        ]);
        $admin->assignRole(Roles::ADMIN);
    }
}
