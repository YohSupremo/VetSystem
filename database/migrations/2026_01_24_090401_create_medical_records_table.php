<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('medical_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pet_id')->constrained()->onDelete('cascade');
        $table->foreignId('veterinarian_id')->constrained('users')->onDelete('cascade');
        $table->text('diagnosis');
        $table->text('treatment');
        $table->text('notes')->nullable();
        $table->date('visit_date');
        $table->date('follow_up_date')->nullable();
        $table->decimal('weight_kg', 5, 2)->nullable();
        $table->decimal('temperature_f', 5, 2)->nullable();
        $table->string('heart_rate')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
