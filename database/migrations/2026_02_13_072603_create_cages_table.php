<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cages', function (Blueprint $table) {
            $table->id();
            $table->string('cage_code', 20)->unique();
            $table->string('location', 100)->nullable();
            $table->enum('size', ['small', 'medium', 'large', 'extra_large'])->default('medium');
            $table->enum('status', [
                'available',
                'occupied',
                'maintenance',
                'out_of_service'
            ])->default('available');
            $table->string('qr_code_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('size');
            $table->index('cage_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cages');
    }
};