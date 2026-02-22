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

        if (!Schema::hasColumn('notification_settings', 'in_app_enabled')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->boolean('in_app_enabled')->default(true)->after('sms_enabled');
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

        if (Schema::hasColumn('notification_settings', 'in_app_enabled')) {
            Schema::table('notification_settings', function (Blueprint $table) {
                $table->dropColumn('in_app_enabled');
            });
        }
    }
};
