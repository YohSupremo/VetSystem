<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {
    // Check if tables exist
    $tables = [
        'pets' => Schema::hasTable('pets'),
        'vaccinations' => Schema::hasTable('vaccinations'),
    ];

    // Get columns if tables exist
    $columns = [];
    
    if ($tables['pets']) {
        $columns['pets'] = Schema::getColumnListing('pets');
    }
    
    if ($tables['vaccinations']) {
        $columns['vaccinations'] = Schema::getColumnListing('vaccinations');
    }

    // Try to get a count of pets with vaccinations
    $petsWithVaccinations = null;
    if ($tables['pets'] && $tables['vaccinations']) {
        try {
            $petsWithVaccinations = DB::table('pets')
                ->join('vaccinations', 'pets.id', '=', 'vaccinations.pet_id')
                ->count();
        } catch (\Exception $e) {
            $petsWithVaccinations = 'Error: ' . $e->getMessage();
        }
    }

    // Check if the relationship works through the model
    $modelCheck = null;
    if (class_exists('App\Models\Pet') && $tables['pets'] && $tables['vaccinations']) {
        try {
            $pet = new App\Models\Pet();
            $modelCheck = method_exists($pet, 'vaccinations') ? 'Vaccinations relationship exists' : 'Vaccinations relationship missing';
        } catch (\Exception $e) {
            $modelCheck = 'Error checking model: ' . $e->getMessage();
        }
    }

    return [
        'tables_exist' => $tables,
        'columns' => $columns,
        'pets_with_vaccinations_count' => $petsWithVaccinations,
        'model_check' => $modelCheck,
    ];
});
