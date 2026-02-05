<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
