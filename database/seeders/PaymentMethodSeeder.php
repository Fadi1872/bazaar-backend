<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'MTN',
                'provider_code' => 'MTN',
            ],
            [
                'name' => 'Syriatel',
                'provider_code' => 'SYR',
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(
                ['provider_code' => $method['provider_code']],
                $method
            );
        }
    }
}
