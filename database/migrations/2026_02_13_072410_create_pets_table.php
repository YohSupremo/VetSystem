<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            
            $table->string('registration_number', 20)->unique()
                  ->comment('User-facing unique identifier (e.g., 2026-000001)');
            
            $table->foreignId('owner_id')->constrained('pet_owners')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('species', 50);
            $table->string('breed', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->string('color', 100)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('microchip_number', 50)->unique()->nullable();
            $table->string('photo_path')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('deceased_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('owner_id');
            $table->index('species');
            $table->index('microchip_number');
            $table->index('is_active');
            $table->index('registration_number', 'idx_pet_registration');
        });

        // Add check constraint for weight
        DB::statement('ALTER TABLE pets ADD CONSTRAINT chk_weight CHECK (weight IS NULL OR (weight > 0 AND weight <= 500))');
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};