<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('schedule_date');
            $table->time('shift_start');
            $table->time('shift_end');
            $table->integer('break_duration_minutes')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('schedule_date');
            $table->index(['user_id', 'schedule_date'], 'idx_schedule_search');
        });

        // Add check constraints
        DB::statement('ALTER TABLE staff_schedules ADD CONSTRAINT chk_break_duration CHECK (break_duration_minutes >= 0)');
        DB::statement('ALTER TABLE staff_schedules ADD CONSTRAINT chk_shift_times CHECK (shift_end > shift_start)');
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_schedules');
    }
};
