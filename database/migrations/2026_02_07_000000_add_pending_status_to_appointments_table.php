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
        // SQLite doesn't support modifying ENUM columns directly
        // We need to recreate the table with the new enum values
        
        // Get all data from the appointments table
        $data = DB::table('appointments')->get();
        
        // Drop the original table
        Schema::dropIfExists('appointments');
        
        // Recreate the table with 'pending' added to the status enum
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('veterinarian_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->enum('status', ['pending', 'scheduled', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->enum('type', ['checkup', 'vaccination', 'surgery', 'dental', 'grooming', 'other']);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->integer('queue_number')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('start_service_time')->nullable();
            $table->timestamp('end_service_time')->nullable();
            $table->timestamps();
            
            // Add indexes for frequently filtered columns
            $table->index(['appointment_date', 'status']);
            $table->index(['veterinarian_id', 'appointment_date']);
        });
        
        // Insert the data back
        foreach ($data as $row) {
            DB::table('appointments')->insert((array) $row);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQLite doesn't support modifying ENUM columns directly
        // We need to recreate the table without 'pending' in the status enum
        
        // Get all data from the appointments table
        $data = DB::table('appointments')->get();
        
        // Drop the current table
        Schema::dropIfExists('appointments');
        
        // Recreate the table with the original enum values
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('veterinarian_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->enum('type', ['checkup', 'vaccination', 'surgery', 'dental', 'grooming', 'other']);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->integer('queue_number')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('start_service_time')->nullable();
            $table->timestamp('end_service_time')->nullable();
            $table->timestamps();
            
            // Add indexes for frequently filtered columns
            $table->index(['appointment_date', 'status']);
            $table->index(['veterinarian_id', 'appointment_date']);
        });
        
        // Insert the data back (convert any 'pending' status to 'scheduled')
        foreach ($data as $row) {
            if ($row->status === 'pending') {
                $row->status = 'scheduled';
            }
            DB::table('appointments')->insert((array) $row);
        }
    }
};
