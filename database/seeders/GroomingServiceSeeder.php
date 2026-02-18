<?php

namespace Database\Seeders;

use App\Models\GroomingService;
use Illuminate\Database\Seeder;

class GroomingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'service_name' => 'Basic Bath',
                'description' => 'Warm bath, gentle shampoo, blow dry, and light brushing.',
                'duration_minutes' => 45,
                'price' => 600.00,
                'is_active' => true,
            ],
            [
                'service_name' => 'Full Groom',
                'description' => 'Bath, haircut, nail trimming, ear cleaning, and blow dry.',
                'duration_minutes' => 90,
                'price' => 1200.00,
                'is_active' => true,
            ],
            [
                'service_name' => 'Nail Trim',
                'description' => 'Safe trimming of nails with quick-check and finishing.',
                'duration_minutes' => 20,
                'price' => 250.00,
                'is_active' => true,
            ],
            [
                'service_name' => 'Ear Cleaning',
                'description' => 'Routine ear cleaning to remove wax and debris.',
                'duration_minutes' => 20,
                'price' => 250.00,
                'is_active' => true,
            ],
            [
                'service_name' => 'Teeth Brushing',
                'description' => 'Gentle dental brushing and oral hygiene care.',
                'duration_minutes' => 15,
                'price' => 200.00,
                'is_active' => true,
            ],
            [
                'service_name' => 'De-shedding Treatment',
                'description' => 'Specialized wash and brushing to reduce excess shedding.',
                'duration_minutes' => 60,
                'price' => 850.00,
                'is_active' => true,
            ],
            [
                'service_name' => 'Flea and Tick Treatment',
                'description' => 'Topical grooming treatment for flea and tick management.',
                'duration_minutes' => 40,
                'price' => 700.00,
                'is_active' => true,
            ],
            [
                'service_name' => 'Paw and Pad Care',
                'description' => 'Cleaning, trimming around pads, and moisturizing care.',
                'duration_minutes' => 25,
                'price' => 300.00,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            GroomingService::updateOrCreate(
                ['service_name' => $service['service_name']],
                $service
            );
        }
    }
}
