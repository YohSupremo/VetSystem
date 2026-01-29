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
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name', 150);
            $table->enum('category', ['blood', 'urine', 'fecal', 'biopsy', 'cytology', 'other']);
            $table->text('description')->nullable();
            $table->decimal('standard_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
