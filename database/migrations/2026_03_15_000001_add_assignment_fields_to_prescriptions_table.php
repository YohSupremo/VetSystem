<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('assigned_staff_id')
                ->nullable()
                ->after('medical_record_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('external_clinic_name', 150)
                ->nullable()
                ->after('instructions');

            $table->string('external_veterinarian_name', 150)
                ->nullable()
                ->after('external_clinic_name');

            $table->index('assigned_staff_id');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropIndex(['assigned_staff_id']);
            $table->dropConstrainedForeignId('assigned_staff_id');
            $table->dropColumn(['external_clinic_name', 'external_veterinarian_name']);
        });
    }
};
