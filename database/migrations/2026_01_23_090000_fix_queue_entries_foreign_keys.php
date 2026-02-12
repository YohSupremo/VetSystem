git reset --hard
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing queue_entries table if it exists
        Schema::dropIfExists('queue_entries');

        // Recreate the queue_entries table with the correct foreign key
        Schema::create('queue_entries', function (Blueprint $table) {
            $table->id();
            
            // Make sure appointments table exists first
            if (Schema::hasTable('appointments')) {
                $table->foreignId('appointment_id')
                    ->nullable()
                    ->constrained('appointments')
                    ->nullOnDelete();
            } else {
                $table->foreignId('appointment_id')->nullable();
            }
            
            $table->foreignId('pet_id')
                ->constrained('pets')
                ->cascadeOnDelete();
                
            $table->dateTime('arrival_time')->nullable();
            $table->enum('status', ['waiting', 'being_served', 'completed'])->default('waiting');
            $table->integer('estimated_wait_time')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_entries');
    }
};
