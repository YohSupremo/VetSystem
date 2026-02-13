<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('veterinarian_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('appointment_date');
            $table->enum('type', [
                'consultation',
                'vaccination',
                'surgery',
                'grooming',
                'boarding',
                'follow_up',
                'emergency',
                'other'
            ])->default('consultation');
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
                'no_show'
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->dateTime('arrival_time')->nullable();
            $table->enum('queue_status', ['waiting', 'being_served', 'completed'])->nullable();
            $table->integer('queue_priority')->default(0);
            $table->integer('estimated_wait_time')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->dateTime('reminder_sent_at')->nullable();
            $table->timestamps();

            $table->index('pet_id');
            $table->index('veterinarian_id');
            $table->index('appointment_date');
            $table->index('status');
            $table->index(['queue_status', 'queue_priority']);
            $table->index(['appointment_date', 'status', 'veterinarian_id'], 'idx_appointment_search');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};