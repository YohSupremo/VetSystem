<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@vetcare.com',
                'username' => 'sarah.johnson',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'address' => '123 Pet Care Lane, Vet City, VC 12345',
                'contact_number' => '+1-555-0101',
                'is_active' => true,
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@vetcare.com',
                'username' => 'michael.chen',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'address' => '456 Surgery Street, Vet City, VC 12345',
                'contact_number' => '+1-555-0102',
                'is_active' => true,
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Rodriguez',
                'email' => 'emily.rodriguez@vetcare.com',
                'username' => 'emily.rodriguez',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'address' => '789 Exotic Avenue, Vet City, VC 12345',
                'contact_number' => '+1-555-0103',
                'is_active' => true,
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Wilson',
                'email' => 'james.wilson@vetcare.com',
                'username' => 'james.wilson',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'address' => '321 Farm Road, Vet City, VC 12345',
                'contact_number' => '+1-555-0104',
                'is_active' => true,
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Anderson',
                'email' => 'lisa.anderson@vetcare.com',
                'username' => 'lisa.anderson',
                'password' => Hash::make('password123'),
                'role' => 'veterinarian',
                'address' => '654 Dental Drive, Vet City, VC 12345',
                'contact_number' => '+1-555-0105',
                'is_active' => false,
            ],
        ];

        foreach ($veterinarians as $vet) {
            User::create($vet);
        }

        $this->command->info('Veterinarian test data created successfully!');
    }
}
