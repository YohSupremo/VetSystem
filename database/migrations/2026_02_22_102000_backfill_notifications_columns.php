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
        if (!Schema::hasTable('notifications')) {
            return;
        }

        if (!Schema::hasColumn('notifications', 'icon')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('icon')->default('bell');
            });
        }

        if (!Schema::hasColumn('notifications', 'method')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->enum('method', ['in_app', 'email', 'sms', 'both'])->default('in_app');
            });
        }

        if (!Schema::hasColumn('notifications', 'priority')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            });
        }

        if (!Schema::hasColumn('notifications', 'scheduled_for')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dateTime('scheduled_for')->nullable();
            });
        }

        if (!Schema::hasColumn('notifications', 'sent_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dateTime('sent_at')->nullable();
            });
        }

        if (!Schema::hasColumn('notifications', 'read_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dateTime('read_at')->nullable();
            });
        }

        if (!Schema::hasColumn('notifications', 'is_read')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->boolean('is_read')->default(false);
            });
        }

        if (!Schema::hasColumn('notifications', 'action_url')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('action_url')->nullable();
            });
        }

        if (!Schema::hasColumn('notifications', 'reference_type')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->enum('reference_type', [
                    'appointment',
                    'pet',
                    'invoice',
                    'prescription',
                    'vaccination',
                    'lab_test',
                    'cage_assignment',
                    'inventory',
                ])->nullable();
            });
        }

        if (!Schema::hasColumn('notifications', 'reference_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('reference_id')->nullable();
            });
        }

        if (!Schema::hasColumn('notifications', 'error_message')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->text('error_message')->nullable();
            });
        }

        if (!Schema::hasColumn('notifications', 'retry_count')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->integer('retry_count')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        $columns = [
            'priority',
            'reference_type',
            'reference_id',
            'error_message',
            'retry_count',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('notifications', $column)) {
                Schema::table('notifications', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
