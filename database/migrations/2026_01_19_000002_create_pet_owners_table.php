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
        Schema::create('pet_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('owner_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('pet_owners')->onDelete('cascade');
            $table->string('contact_name');
            $table->string('contact_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_emergency_contacts');
        Schema::dropIfExists('pet_owners');
    }
};
