<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RoleTestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('PawCare123');

        $accounts = [
            ['username' => 'vet_demo', 'email' => 'vet.demo@vetclinic.com', 'role' => 'veterinarian', 'first_name' => 'Vet', 'last_name' => 'Demo'],
            ['username' => 'staff_demo', 'email' => 'staff.demo@vetclinic.com', 'role' => 'staff', 'first_name' => 'Staff', 'last_name' => 'Demo'],
            ['username' => 'reception_demo', 'email' => 'reception.demo@vetclinic.com', 'role' => 'reception', 'first_name' => 'Reception', 'last_name' => 'Demo'],
            ['username' => 'pharmacy_demo', 'email' => 'pharmacy.demo@vetclinic.com', 'role' => 'pharmacy', 'first_name' => 'Pharmacy', 'last_name' => 'Demo'],
            ['username' => 'groomer_demo', 'email' => 'groomer.demo@vetclinic.com', 'role' => 'groomer', 'first_name' => 'Groomer', 'last_name' => 'Demo'],
            ['username' => 'boarding_demo', 'email' => 'boarding.demo@vetclinic.com', 'role' => 'boarding', 'first_name' => 'Boarding', 'last_name' => 'Demo'],
        ];

        foreach ($accounts as $account) {
            DB::table('users')->updateOrInsert(
                ['username' => $account['username']],
                [
                    'email' => $account['email'],
                    'password' => $password,
                    'role' => $account['role'],
                    'first_name' => $account['first_name'],
                    'last_name' => $account['last_name'],
                    'is_active' => 1,
                    'email_verified' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
