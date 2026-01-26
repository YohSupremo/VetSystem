<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
    'admin',
    'veterinarian',
    'receptionist',
    'pharmacist',
    'pet_owner',
    'boarding',
];

        $streets = [
            'Rizal St.',
            'Bonifacio Ave.',
            'Mabini St.',
            'Del Pilar St.',
            'Quezon Blvd.',
            'Taft Ave.',
        ];

        $cities = [
            'Manila',
            'Quezon City',
            'Cebu City',
            'Davao City',
            'Baguio City',
            'Iloilo City',
        ];

        $users = [];

        foreach ($roles as $role) {
            for ($i = 1; $i <= 5; $i++) {

                $address = rand(1, 999) . ' ' .
                           $streets[array_rand($streets)] . ', ' .
                           $cities[array_rand($cities)];

                $users[] = [
                    'username' => "{$role}{$i}",
                    'email' => "{$role}{$i}@clinic.test",
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'first_name' => ucfirst($role),
                    'last_name' => "User{$i}",
                    'contact_number' => '09' . rand(100000000, 999999999),
                    'address' => $address, // ✅ RANDOM ADDRESS
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('users')->insert($users);
    }
}
