<?php

namespace Database\Seeders;

use App\Models\Cage;
use Illuminate\Database\Seeder;

class CagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $cages = [
            ['cage_code' => 'A-01', 'location' => 'Ward A', 'size' => 'small', 'status' => 'available'],
            ['cage_code' => 'A-02', 'location' => 'Ward A', 'size' => 'small', 'status' => 'available'],
            ['cage_code' => 'A-03', 'location' => 'Ward A', 'size' => 'small', 'status' => 'maintenance'],
            ['cage_code' => 'A-04', 'location' => 'Ward A', 'size' => 'medium', 'status' => 'available'],
            ['cage_code' => 'A-05', 'location' => 'Ward A', 'size' => 'medium', 'status' => 'available'],
            ['cage_code' => 'B-01', 'location' => 'Ward B', 'size' => 'medium', 'status' => 'available'],
            ['cage_code' => 'B-02', 'location' => 'Ward B', 'size' => 'medium', 'status' => 'occupied'],
            ['cage_code' => 'B-03', 'location' => 'Ward B', 'size' => 'large', 'status' => 'available'],
            ['cage_code' => 'B-04', 'location' => 'Ward B', 'size' => 'large', 'status' => 'available'],
            ['cage_code' => 'B-05', 'location' => 'Ward B', 'size' => 'large', 'status' => 'out_of_service'],
            ['cage_code' => 'C-01', 'location' => 'Ward C', 'size' => 'small', 'status' => 'available'],
            ['cage_code' => 'C-02', 'location' => 'Ward C', 'size' => 'medium', 'status' => 'available'],
            ['cage_code' => 'C-03', 'location' => 'Ward C', 'size' => 'medium', 'status' => 'available'],
            ['cage_code' => 'C-04', 'location' => 'Ward C', 'size' => 'large', 'status' => 'available'],
            ['cage_code' => 'C-05', 'location' => 'Ward C', 'size' => 'extra_large', 'status' => 'available'],
            ['cage_code' => 'D-01', 'location' => 'Ward D', 'size' => 'small', 'status' => 'available'],
            ['cage_code' => 'D-02', 'location' => 'Ward D', 'size' => 'small', 'status' => 'available'],
            ['cage_code' => 'D-03', 'location' => 'Ward D', 'size' => 'medium', 'status' => 'available'],
            ['cage_code' => 'D-04', 'location' => 'Ward D', 'size' => 'large', 'status' => 'available'],
            ['cage_code' => 'D-05', 'location' => 'Ward D', 'size' => 'extra_large', 'status' => 'available'],
        ];

        $payload = array_map(function (array $cage) use ($now) {
            return $cage + ['created_at' => $now, 'updated_at' => $now];
        }, $cages);

        Cage::insert($payload);
    }
}
