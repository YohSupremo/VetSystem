<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'appointment_reminder',
                'vaccination_due',
                'payment_due',
                'payment_overdue',
                'lab_result',
                'prescription_refill',
                'boarding_checkout',
                'low_stock',
                'item_expiry'
            ]);
            $table->string('title');
            $table->text('message');
            $table->enum('method', ['email', 'sms', 'both'])->default('email');
            $table->enum('status', ['pending', 'sent', 'failed', 'read'])->default('pending');
            $table->dateTime('scheduled_for');
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->enum('reference_type', [
                'appointment',
                'pet',
                'invoice',
                'prescription',
                'vaccination',
                'lab_test',
                'cage_assignment',
                'inventory'
            ])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('status');
            $table->index('scheduled_for');
            $table->index(['status', 'scheduled_for'], 'idx_notification_queue');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};