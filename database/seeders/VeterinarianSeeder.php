<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VeterinarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $veterinarians = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'username' => 'drsmith',
                'email' => 'dr.smith@vetclinic.com',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'is_active' => 1,
                'contact_number' => '123-456-7890',
                'address' => '123 Vet Clinic St., City',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'username' => 'drjohnson',
                'email' => 'dr.johnson@vetclinic.com',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'is_active' => 1,
                'contact_number' => '123-456-7891',
                'address' => '124 Vet Clinic St., City',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'username' => 'drbrown',
                'email' => 'dr.brown@vetclinic.com',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'is_active' => 1,
                'contact_number' => '123-456-7892',
                'address' => '125 Vet Clinic St., City',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'username' => 'drdavis',
                'email' => 'dr.davis@vetclinic.com',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'is_active' => 1,
                'contact_number' => '123-456-7893',
                'address' => '126 Vet Clinic St., City',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Wilson',
                'username' => 'drwilson',
                'email' => 'dr.wilson@vetclinic.com',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'is_active' => 1,
                'contact_number' => '123-456-7894',
                'address' => '127 Vet Clinic St., City',
            ],
        ];

        foreach ($veterinarians as $vet) {
            // Check if veterinarian already exists
            $existingVet = User::where('email', $vet['email'])->first();
            if (!$existingVet) {
                User::create($vet);
                echo "✅ Created: Dr. {$vet['first_name']} {$vet['last_name']}\n";
            } else {
                echo "⏭️  Skipped: Dr. {$vet['first_name']} {$vet['last_name']} (already exists)\n";
            }
        }
    }
}
