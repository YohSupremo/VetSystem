<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['username' => 'customer_demo'],
            [
                'first_name' => 'Customer',
                'last_name' => 'Demo',
                'email' => 'customer.demo@vetclinic.com',
                'password' => Hash::make('PawCare123'),
                'address' => 'Demo Street, Manila',
                'contact_number' => '09179990000',
                'role' => 'pet_owner',
                'is_active' => true,
                'email_verified' => true,
                'phone_verified' => true,
            ]
        );

        $owner = PetOwner::updateOrCreate(
            ['user_id' => $user->id],
            [
                'notes' => 'Demo customer account for testing with sample pets.',
                'preferred_contact_method' => 'email',
                'emergency_contact_name' => 'Demo Emergency Contact',
                'emergency_contact_phone' => '09170001111',
                'emergency_contact_relationship' => 'Sibling',
            ]
        );

        $pets = [
            [
                'name' => 'Buddy Demo',
                'species' => 'Dog',
                'breed' => 'Golden Retriever',
                'birth_date' => now()->subYears(4)->toDateString(),
                'gender' => 'male',
                'color' => 'Golden',
                'weight' => 28.50,
                'is_active' => true,
            ],
            [
                'name' => 'Milo Demo',
                'species' => 'Cat',
                'breed' => 'Domestic Shorthair',
                'birth_date' => now()->subYears(2)->toDateString(),
                'gender' => 'male',
                'color' => 'Tabby',
                'weight' => 4.80,
                'is_active' => true,
            ],
        ];

        foreach ($pets as $petData) {
            Pet::updateOrCreate(
                [
                    'owner_id' => $owner->id,
                    'name' => $petData['name'],
                ],
                $petData
            );
        }
    }
}
