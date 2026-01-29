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
        Schema::create('medication_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->references('id')->on('pets')->onDelete('cascade');
            $table->text('instructions');
              $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_instructions');
    }
};
