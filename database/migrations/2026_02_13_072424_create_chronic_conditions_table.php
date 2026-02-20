<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chronic_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->string('condition_name', 150);
            $table->date('diagnosed_date')->nullable();
            $table->text('ongoing_treatment')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('pet_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chronic_conditions');
    }
};
