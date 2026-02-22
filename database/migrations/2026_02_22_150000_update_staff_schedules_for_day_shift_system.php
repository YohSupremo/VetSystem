<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table exists
        if (!Schema::hasTable('staff_schedules')) {
            // Create table from scratch with new structure
            Schema::create('staff_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
                $table->enum('shift', ['morning', 'night'])->comment('morning: 9AM-5PM, night: 5PM-12AM');
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->unique(['user_id', 'day_of_week', 'shift'], 'unique_staff_schedule');
            });
        } else {
            // Drop existing constraints if they exist
            try {
                DB::statement('ALTER TABLE staff_schedules DROP CONSTRAINT IF EXISTS chk_break_duration');
                DB::statement('ALTER TABLE staff_schedules DROP CONSTRAINT IF EXISTS chk_shift_times');
            } catch (\Exception $e) {
                // Constraints may not exist, continue
            }

            Schema::table('staff_schedules', function (Blueprint $table) {
                // Drop old columns
                $table->dropColumn(['schedule_date', 'shift_start', 'shift_end', 'break_duration_minutes']);
                
                // Add new columns
                $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])->after('user_id');
                $table->enum('shift', ['morning', 'night'])->after('day_of_week')->comment('morning: 9AM-5PM, night: 5PM-12AM');
                $table->boolean('is_active')->default(true)->after('shift');
            });

            // Add unique constraint to prevent duplicate schedules
            Schema::table('staff_schedules', function (Blueprint $table) {
                $table->unique(['user_id', 'day_of_week', 'shift'], 'unique_staff_schedule');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('staff_schedules')) {
            Schema::table('staff_schedules', function (Blueprint $table) {
                $table->dropUnique('unique_staff_schedule');
                $table->dropColumn(['day_of_week', 'shift', 'is_active']);
                
                // Restore old columns
                $table->date('schedule_date');
                $table->time('shift_start');
                $table->time('shift_end');
                $table->integer('break_duration_minutes')->default(0);
            });

            // Re-add old constraints
            DB::statement('ALTER TABLE staff_schedules ADD CONSTRAINT chk_break_duration CHECK (break_duration_minutes >= 0)');
            DB::statement('ALTER TABLE staff_schedules ADD CONSTRAINT chk_shift_times CHECK (shift_end > shift_start)');
        }
    }
};
