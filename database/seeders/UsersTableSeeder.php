<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $roles = [
            'admin' => [
                ['Alice','Reyes','09171234567','123 Main St, Manila'],
                ['Bob','Santos','09171234568','124 Main St, Manila'],
                ['Clara','Lopez','09171234569','125 Main St, Manila'],
                ['Daniel','Garcia','09171234570','126 Main St, Manila'],
                ['Ella','Martinez','09171234571','127 Main St, Manila'],
                ['Felix','Tan','09171234572','128 Main St, Manila'],
                ['Grace','Cruz','09171234573','129 Main St, Manila'],
                ['Henry','Lim','09171234574','130 Main St, Manila'],
                ['Ivy','Torres','09171234575','131 Main St, Manila'],
                ['Jack','Mendoza','09171234576','132 Main St, Manila'],
            ],
            'reception' => [
                ['Maria','Lopez','09211234567','55 Sunrise St, Manila'],
                ['Lara','Santos','09211234568','56 Sunrise St, Manila'],
                ['Nina','Torres','09211234569','57 Sunrise St, Manila'],
                ['Omar','Garcia','09211234570','58 Sunrise St, Manila'],
                ['Paula','Lim','09211234571','59 Sunrise St, Manila'],
                ['Quinn','Mendoza','09211234572','60 Sunrise St, Manila'],
                ['Rico','Tan','09211234573','61 Sunrise St, Manila'],
                ['Sophia','Martinez','09211234574','62 Sunrise St, Manila'],
                ['Tom','Cruz','09211234575','63 Sunrise St, Manila'],
                ['Ula','Reyes','09211234576','64 Sunrise St, Manila'],
            ],
            'pharmacy' => [
                ['Kevin','Chua','09221234567','77 Health Blvd, Makati'],
                ['Lena','Santos','09221234568','78 Health Blvd, Makati'],
                ['Milo','Tan','09221234569','79 Health Blvd, Makati'],
                ['Nina','Lopez','09221234570','80 Health Blvd, Makati'],
                ['Oscar','Cruz','09221234571','81 Health Blvd, Makati'],
                ['Paula','Lim','09221234572','82 Health Blvd, Makati'],
                ['Quinn','Torres','09221234573','83 Health Blvd, Makati'],
                ['Rico','Mendoza','09221234574','84 Health Blvd, Makati'],
                ['Sophia','Garcia','09221234575','85 Health Blvd, Makati'],
                ['Tom','Martinez','09221234576','86 Health Blvd, Makati'],
            ],
            'pet_owner' => [
                ['Karen','Santos','09251234567','12 Happy St, Manila'],
                ['Mike','Reyes','09251234568','13 Happy St, Manila'],
                ['Lara','Lopez','09251234569','14 Happy St, Manila'],
                ['Nina','Torres','09251234570','15 Happy St, Manila'],
                ['Oscar','Garcia','09251234571','16 Happy St, Manila'],
                ['Paula','Lim','09251234572','17 Happy St, Manila'],
                ['Quinn','Mendoza','09251234573','18 Happy St, Manila'],
                ['Rico','Tan','09251234574','19 Happy St, Manila'],
                ['Sophia','Martinez','09251234575','20 Happy St, Manila'],
                ['Tom','Cruz','09251234576','21 Happy St, Manila'],
            ],
            'boarding' => [
                ['Rico','Garcia','09241234567','33 Pet Lane, Pasig'],
                ['Sara','Santos','09241234568','34 Pet Lane, Pasig'],
                ['Tom','Lopez','09241234569','35 Pet Lane, Pasig'],
                ['Uma','Tan','09241234570','36 Pet Lane, Pasig'],
                ['Vera','Lim','09241234571','37 Pet Lane, Pasig'],
                ['Will','Mendoza','09241234572','38 Pet Lane, Pasig'],
                ['Xander','Martinez','09241234573','39 Pet Lane, Pasig'],
                ['Yara','Cruz','09241234574','40 Pet Lane, Pasig'],
                ['Zane','Torres','09241234575','41 Pet Lane, Pasig'],
                ['Ava','Reyes','09241234576','42 Pet Lane, Pasig'],
            ],
            'groomer' => [
                ['Maya','Reyes','09261234567','21 Groom St, Manila'],
                ['Leo','Tan','09261234568','22 Groom St, Manila'],
                ['Zara','Lopez','09261234569','23 Groom St, Manila'],
                ['Noah','Cruz','09261234570','24 Groom St, Manila'],
                ['Ella','Garcia','09261234571','25 Groom St, Manila'],
                ['Liam','Martinez','09261234572','26 Groom St, Manila'],
                ['Sophie','Lim','09261234573','27 Groom St, Manila'],
                ['Ethan','Mendoza','09261234574','28 Groom St, Manila'],
                ['Chloe','Torres','09261234575','29 Groom St, Manila'],
                ['Ryan','Santos','09261234576','30 Groom St, Manila'],
            ],
            'staff' => [
                ['General','Staff','09271234567','15 General St, Manila'],
                ['Support','Worker','09271234568','16 General St, Manila'],
                ['Operations','Manager','09271234569','17 General St, Manila'],
                ['Assistant','Admin','09271234570','18 General St, Manila'],
                ['Office','Helper','09271234571','19 General St, Manila'],
            ],
        ];

        foreach($roles as $role => $users){
            foreach($users as $user){
                DB::table('users')->insert([
                    'username' => strtolower($user[0]).'_'.strtolower($user[1]),
                    'email' => strtolower($user[0]).'.'.strtolower($user[1]).'@vetclinic.com',
                    'password' => Hash::make('password123'),
                    'role' => DB::raw("'" . $role . "'"), // ENUM safe insert
                    'first_name' => $user[0],
                    'last_name' => $user[1],
                    'contact_number' => $user[2],
                    'address' => $user[3],
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
