<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('notifications') || !Schema::hasColumn('notifications', 'method')) {
            return;
        }

        $column = DB::selectOne("SHOW COLUMNS FROM notifications LIKE 'method'");
        $columnType = strtolower((string) ($column->Type ?? ''));

        if (!str_contains($columnType, "'in_app'")) {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN method ENUM('in_app','email','sms','both') NOT NULL DEFAULT 'in_app'");
            return;
        }

        DB::statement("ALTER TABLE notifications MODIFY COLUMN method ENUM('in_app','email','sms','both') NOT NULL DEFAULT 'in_app'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('notifications') || !Schema::hasColumn('notifications', 'method')) {
            return;
        }

        DB::statement("ALTER TABLE notifications MODIFY COLUMN method ENUM('email','sms','both') NOT NULL DEFAULT 'email'");
    }
};
