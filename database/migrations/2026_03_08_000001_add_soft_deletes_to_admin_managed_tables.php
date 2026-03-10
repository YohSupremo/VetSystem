<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables primarily deleted from admin modules.
     */
    private array $tables = [
        'users',
        'pet_owners',
        'pets',
        'appointments',
        'medical_records',
        'chronic_conditions',
        'pet_allergies',
        'prescriptions',
        'surgeries',
        'pet_vaccinations',
        'inventory_items',
        'lab_tests',
        'lab_requisitions',
        'grooming_services',
        'grooming_appointments',
        'cage_assignments',
        'invoices',
        'invoice_items',
        'payments',
        'staff_schedules',
        'incidents',
        'notifications',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (!Schema::hasColumn($table, 'deleted_at')) {
                    $blueprint->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropSoftDeletes();
            });
        }
    }
};
