<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_settings', function (Blueprint $table) {
            if (Schema::hasColumn('clinic_settings', 'appointment_slot_minutes')) {
                $table->dropColumn('appointment_slot_minutes');
            }
            if (Schema::hasColumn('clinic_settings', 'appointment_buffer_minutes')) {
                $table->dropColumn('appointment_buffer_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clinic_settings', function (Blueprint $table) {
            $table->integer('appointment_slot_minutes')->default(30);
            $table->integer('appointment_buffer_minutes')->default(10);
        });
    }
};
