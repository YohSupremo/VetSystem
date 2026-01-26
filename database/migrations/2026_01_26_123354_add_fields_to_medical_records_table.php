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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->foreignId('veterinarian_id')->constrained('users')->onDelete('cascade');
            $table->date('visit_date');
            $table->text('complaint'); // Patient complaints
            $table->text('examination_notes')->nullable(); // Examination notes
            $table->json('vital_signs')->nullable(); // Vital signs (temperature, heart rate, respiratory rate, blood pressure, etc.)
            $table->text('diagnosis')->nullable(); // Diagnosis documentation
            $table->text('treatment_plan')->nullable(); // Treatment plan
            $table->date('follow_up_date')->nullable(); // Follow-up scheduling
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['pet_id']);
            $table->dropForeign(['veterinarian_id']);
            $table->dropColumn([
                'pet_id',
                'veterinarian_id',
                'visit_date',
                'complaint',
                'examination_notes',
                'vital_signs',
                'diagnosis',
                'treatment_plan',
                'follow_up_date'
            ]);
        });
    }
};
