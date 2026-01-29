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
        Schema::table('cage_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('cage_assignments', 'medication_notes')) {
                $table->text('medication_notes')->nullable()->after('end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cage_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('cage_assignments', 'medication_notes')) {
                $table->dropColumn('medication_notes');
            }
        });
    }
};

