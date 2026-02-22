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

        if (!Schema::hasColumn('notification_settings', 'appointment_reminder_hours')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->integer('appointment_reminder_hours')->default(24);
            });
        }

        if (!Schema::hasColumn('notification_settings', 'vaccination_due_days')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->integer('vaccination_due_days')->default(7);
            });
        }

        if (!Schema::hasColumn('notification_settings', 'default_advance_hours')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->integer('default_advance_hours')->default(24);
            });
        }

        if (!Schema::hasColumn('notification_settings', 'quiet_hours_enabled')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->boolean('quiet_hours_enabled')->default(false);
            });
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
            'appointment_reminder_hours',
            'vaccination_due_days',
            'default_advance_hours',
            'quiet_hours_enabled',
            'quiet_hours_start',
            'quiet_hours_end',
            'notification_frequency',
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
