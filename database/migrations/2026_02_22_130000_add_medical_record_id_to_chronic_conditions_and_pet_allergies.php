<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chronic_conditions', function (Blueprint $table) {
            $table->foreignId('medical_record_id')
                ->nullable()
                ->after('pet_id')
                ->constrained('medical_records')
                ->nullOnDelete();

            $table->index('medical_record_id', 'idx_chronic_medical_record');
        });

        Schema::table('pet_allergies', function (Blueprint $table) {
            $table->foreignId('medical_record_id')
                ->nullable()
                ->after('pet_id')
                ->constrained('medical_records')
                ->nullOnDelete();

            $table->index('medical_record_id', 'idx_allergy_medical_record');
        });
    }

    public function down(): void
    {
        Schema::table('pet_allergies', function (Blueprint $table) {
            $table->dropForeign(['medical_record_id']);
            $table->dropIndex('idx_allergy_medical_record');
            $table->dropColumn('medical_record_id');
        });

        Schema::table('chronic_conditions', function (Blueprint $table) {
            $table->dropForeign(['medical_record_id']);
            $table->dropIndex('idx_chronic_medical_record');
            $table->dropColumn('medical_record_id');
        });
    }
};
