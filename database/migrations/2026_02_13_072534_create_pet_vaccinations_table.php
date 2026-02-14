<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('vaccine_id')->constrained()->onDelete('restrict');
            $table->string('batch_number', 50)->nullable();
            $table->integer('dose_number')->default(1);
            $table->date('administered_date');
            $table->date('next_due_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('administered_by')->constrained('users')->onDelete('restrict');
            $table->string('certificate_path')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index('pet_id');
            $table->index('vaccine_id');
            $table->index('next_due_date');
            $table->index(['pet_id', 'next_due_date'], 'idx_vaccination_search');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_vaccinations');
    }
};