<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->string('medication_name', 150);
            $table->string('dosage', 100);
            $table->string('frequency', 100);
            $table->integer('duration_days');
            $table->integer('quantity');
            $table->text('instructions')->nullable();
            $table->boolean('dispensed')->default(false);
            $table->dateTime('dispensed_at')->nullable();
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('refill_reminder_sent')->default(false);
            $table->timestamps();

            $table->index('medical_record_id');
            $table->index('dispensed');
        });

        // Add check constraints
        DB::statement('ALTER TABLE prescriptions ADD CONSTRAINT chk_duration CHECK (duration_days > 0 AND duration_days <= 365)');
        DB::statement('ALTER TABLE prescriptions ADD CONSTRAINT chk_quantity CHECK (quantity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};