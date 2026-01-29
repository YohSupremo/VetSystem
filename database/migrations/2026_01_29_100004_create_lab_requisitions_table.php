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
        Schema::create('lab_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('lab_tests')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('requested_date')->default(now());
            $table->boolean('sample_collected')->default(false);
            $table->dateTime('sample_collection_date')->nullable();
            $table->enum('status', ['pending', 'collected', 'sent_to_lab', 'completed', 'cancelled'])->default('pending');
            $table->text('results')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_requisitions');
    }
};
