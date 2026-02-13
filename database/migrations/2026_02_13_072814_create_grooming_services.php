<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grooming_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name', 150);
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // Add check constraints
        DB::statement('ALTER TABLE grooming_services ADD CONSTRAINT chk_grooming_duration CHECK (duration_minutes > 0)');
        DB::statement('ALTER TABLE grooming_services ADD CONSTRAINT chk_grooming_price CHECK (price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('grooming_services');
    }
};