<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grooming_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained('grooming_services')->onDelete('restrict');
            $table->foreignId('groomer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('special_instructions')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('actual_duration_minutes')->nullable();
            $table->timestamps();

            $table->index('appointment_id');
            $table->index('groomer_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grooming_appointments');
    }
};