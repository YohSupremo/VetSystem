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
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->foreignId('medical_record_id')->nullable()->constrained('medical_records')->onDelete('set null');
            $table->foreignId('surgeon_id')->constrained('users')->onDelete('restrict');
            $table->dateTime('scheduled_date');
            $table->string('procedure_name');
            $table->string('anesthesia_type')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('pre_op_notes')->nullable();
            $table->text('surgery_notes')->nullable();
            $table->text('post_op_instructions')->nullable();
            $table->text('outcome')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surgeries');
    }
};
