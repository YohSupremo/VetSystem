<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cage_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cage_id')->constrained()->onDelete('restrict');
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->text('feeding_schedule')->nullable();
            $table->string('feeding_times')->nullable();
            $table->text('special_diet_notes')->nullable();
            $table->text('medication_instructions')->nullable();
            $table->string('medication_times')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->boolean('checkout_reminder_sent')->default(false);
            $table->timestamps();

            $table->index('cage_id');
            $table->index('pet_id');
            $table->index(['start_date', 'end_date']);
            $table->index(['pet_id', 'start_date', 'end_date'], 'idx_boarding_search');
        });

        // Add check constraints
        DB::statement('ALTER TABLE cage_assignments ADD CONSTRAINT chk_dates CHECK (end_date >= start_date)');
        DB::statement('ALTER TABLE cage_assignments ADD CONSTRAINT chk_daily_rate CHECK (daily_rate IS NULL OR daily_rate >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cage_assignments');
    }
};