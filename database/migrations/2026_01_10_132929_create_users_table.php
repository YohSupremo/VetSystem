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
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('role',
            ['admin', 
            'veterinarian',
            'receptionist', 
            'pharmacist', 
            'pet_owner', 
            'boarding']
            )->default('pet_owner');
            $table->string('address');
            $table->string('contact_number');
            $table->string('email');
            $table->string('username');
            $table->string('password');
            $table->tinyInteger('is_active')->default('1');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
