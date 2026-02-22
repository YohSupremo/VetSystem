<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop unused tables
        Schema::dropIfExists('staff_schedules');
        Schema::dropIfExists('qr_scan_logs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate qr_scan_logs table
        Schema::create('qr_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('scan_type', ['cage', 'pet']);
            $table->foreignId('cage_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('pet_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('scanned_by')->constrained('users')->onDelete('restrict');
            $table->dateTime('scan_timestamp')->useCurrent();
            $table->string('location', 100)->nullable();
            $table->text('notes')->nullable();

            $table->index('cage_id');
            $table->index('pet_id');
            $table->index('scanned_by');
            $table->index('scan_timestamp');
            $table->index('scan_type');
        });

        // Recreate staff_schedules table
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
};
