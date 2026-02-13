<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_id')->constrained('lab_tests')->onDelete('restrict');
            $table->foreignId('requested_by')->constrained('users')->onDelete('restrict');
            $table->dateTime('requested_date')->useCurrent();
            $table->boolean('sample_collected')->default(false);
            $table->dateTime('sample_collection_date')->nullable();
            $table->enum('status', [
                'pending',
                'collected',
                'sent_to_lab',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->text('results')->nullable();
            $table->dateTime('result_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('result_notification_sent')->default(false);
            $table->timestamps();

            $table->index('medical_record_id');
            $table->index('test_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_requisitions');
    }
};