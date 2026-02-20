<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->string('allergen', 150);
            $table->string('reaction_type', 100)->nullable();
            $table->enum('severity', ['mild', 'moderate', 'severe'])->default('mild');
            $table->date('diagnosed_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('pet_id');
            $table->index('severity');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_allergies');
    }
};
