<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_settings', function (Blueprint $table) {
            $table->time('morning_shift_start')->default('09:00')->after('appointment_buffer_minutes');
            $table->time('morning_shift_end')->default('17:00')->after('morning_shift_start');
            $table->time('night_shift_start')->default('17:00')->after('morning_shift_end');
            $table->time('night_shift_end')->default('00:00')->after('night_shift_start');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_settings', function (Blueprint $table) {
            $table->dropColumn([
                'morning_shift_start',
                'morning_shift_end',
                'night_shift_start',
                'night_shift_end',
            ]);
        });
    }
};
