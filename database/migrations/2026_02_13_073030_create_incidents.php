<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number', 50)->unique();
            $table->dateTime('incident_date');
            $table->enum('incident_type', [
                'pet_injury',
                'pet_illness',
                'pet_escape',
                'pet_aggression',
                'staff_injury',
                'visitor_injury',
                'medication_error',
                'equipment_failure',
                'facility_damage',
                'other'
            ]);
            $table->enum('severity', ['minor', 'moderate', 'severe', 'critical']);
            $table->foreignId('pet_id')->nullable()->constrained()->onDelete('set null')->comment('Pet involved if applicable');
            $table->foreignId('affected_user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Staff/visitor affected if applicable');
            $table->string('location', 150);
            $table->foreignId('cage_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description');
            $table->text('immediate_action_taken')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('corrective_action')->nullable();
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
            $table->dateTime('resolved_date')->nullable();
            $table->foreignId('reported_by')->constrained('users')->onDelete('restrict');
            $table->dateTime('reported_at')->useCurrent();
            $table->timestamps();

            $table->index('incident_number');
            $table->index('incident_date');
            $table->index('incident_type');
            $table->index('severity');
            $table->index('status');
            $table->index('pet_id');
            $table->index('affected_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};