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
        Schema::create('cage_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignid('cage_id')->references('id')->on('cages')->onDelete('cascade');
            $table->foreignid('pet_id')->references('id')->on('pets')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
         
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cage_assignments');
    }
};
