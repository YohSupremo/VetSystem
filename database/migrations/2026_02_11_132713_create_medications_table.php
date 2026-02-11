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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('strength')->nullable();
            $table->string('form')->nullable(); // tablet, capsule, liquid, injection, etc.
            $table->string('category')->nullable();
            $table->string('manufacturer')->nullable();
            $table->boolean('requires_prescription')->default(true);
            $table->text('side_effects')->nullable();
            $table->text('dosage_instructions')->nullable();
            $table->text('contraindications')->nullable();
            $table->string('storage_requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
