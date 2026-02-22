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
        if (!Schema::hasTable('notification_settings')) {
            return;
        }

        $booleanColumns = [
            'notifications_enabled' => true,
            'email_enabled' => true,
            'sms_enabled' => false,
            'in_app_enabled' => true,
            'appointment_reminder_enabled' => true,
            'vaccination_due_enabled' => true,
            'payment_due_enabled' => true,
            'payment_overdue_enabled' => true,
            'lab_result_enabled' => true,
            'prescription_refill_enabled' => true,
            'boarding_checkout_enabled' => true,
            'low_stock_enabled' => true,
            'item_expiry_enabled' => true,
            'incident_report_enabled' => true,
            'surgery_status_enabled' => true,
            'grooming_status_enabled' => true,
            'medication_dispensing_enabled' => true,
            'inventory_alert_enabled' => true,
            'quiet_hours_enabled' => false,
        ];

        foreach ($booleanColumns as $column => $default) {
            if (!Schema::hasColumn('notification_settings', $column)) {
                Schema::table('notification_settings', function (Blueprint $table) use ($column, $default) {
                    $table->boolean($column)->default($default);
                });
            }
        }

        $integerColumns = [
            'appointment_reminder_hours' => 24,
            'vaccination_due_days' => 7,
            'default_advance_hours' => 24,
        ];

        foreach ($integerColumns as $column => $default) {
            if (!Schema::hasColumn('notification_settings', $column)) {
                Schema::table('notification_settings', function (Blueprint $table) use ($column, $default) {
                    $table->integer($column)->default($default);
                });
            }
        }

        if (!Schema::hasColumn('notification_settings', 'quiet_hours_start')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->time('quiet_hours_start')->default('22:00:00');
            });
        }

        if (!Schema::hasColumn('notification_settings', 'quiet_hours_end')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->time('quiet_hours_end')->default('08:00:00');
            });
        }

        if (!Schema::hasColumn('notification_settings', 'notification_frequency')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->enum('notification_frequency', ['immediate', 'hourly', 'daily'])->default('immediate');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('notification_settings')) {
            return;
        }

        $columns = [
            'incident_report_enabled',
            'surgery_status_enabled',
            'grooming_status_enabled',
            'medication_dispensing_enabled',
            'inventory_alert_enabled',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('notification_settings', $column)) {
                Schema::table('notification_settings', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
