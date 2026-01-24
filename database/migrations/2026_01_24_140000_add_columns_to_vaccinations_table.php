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
        Schema::table('vaccinations', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('vaccinations', 'pet_id')) {
                $table->foreignId('pet_id')->after('id')->constrained('pets')->onDelete('cascade');
            }
            if (!Schema::hasColumn('vaccinations', 'vaccine_name')) {
                $table->string('vaccine_name')->after('pet_id');
            }
            if (!Schema::hasColumn('vaccinations', 'vaccination_date')) {
                $table->date('vaccination_date')->after('vaccine_name');
            }
            if (!Schema::hasColumn('vaccinations', 'next_due_date')) {
                $table->date('next_due_date')->nullable()->after('vaccination_date');
            }
            if (!Schema::hasColumn('vaccinations', 'veterinarian_id')) {
                $table->foreignId('veterinarian_id')->nullable()->after('next_due_date')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('vaccinations', 'batch_number')) {
                $table->string('batch_number')->nullable()->after('veterinarian_id');
            }
            if (!Schema::hasColumn('vaccinations', 'route_of_administration')) {
                $table->enum('route_of_administration', ['intramuscular', 'subcutaneous', 'intranasal', 'oral'])->nullable()->after('batch_number');
            }
            if (!Schema::hasColumn('vaccinations', 'site_of_injection')) {
                $table->string('site_of_injection')->nullable()->after('route_of_administration');
            }
            if (!Schema::hasColumn('vaccinations', 'adverse_reactions')) {
                $table->text('adverse_reactions')->nullable()->after('site_of_injection');
            }
            if (!Schema::hasColumn('vaccinations', 'notes')) {
                $table->text('notes')->nullable()->after('adverse_reactions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeignKey(['veterinarian_id']);
            $table->dropForeignKey(['pet_id']);
            
            // Then drop columns
            $table->dropColumn([
                'pet_id',
                'vaccine_name',
                'vaccination_date',
                'next_due_date',
                'veterinarian_id',
                'batch_number',
                'route_of_administration',
                'site_of_injection',
                'adverse_reactions',
                'notes',
            ]);
        });
    }
};
