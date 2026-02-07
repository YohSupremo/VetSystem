<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the table exists and if 'pending' is not already in the enum
        $result = DB::select("SHOW COLUMNS FROM appointments WHERE Field = 'status'");
        
        if (!empty($result) && !str_contains($result[0]->Type, 'pending')) {
            // Modify the status enum to include 'pending'
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'scheduled', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum without 'pending'
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled'");
    }
};
