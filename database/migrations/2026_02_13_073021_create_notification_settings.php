<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('appointment_reminder_enabled')->default(true);
            $table->boolean('vaccination_due_enabled')->default(true);
            $table->boolean('payment_due_enabled')->default(true);
            $table->boolean('payment_overdue_enabled')->default(true);
            $table->boolean('lab_result_enabled')->default(true);
            $table->boolean('prescription_refill_enabled')->default(true);
            $table->boolean('boarding_checkout_enabled')->default(true);
            $table->boolean('low_stock_enabled')->default(true);
            $table->boolean('item_expiry_enabled')->default(true);
            $table->integer('default_advance_hours')->default(24);
            $table->timestamps();

            $table->unique('user_id', 'unique_user_settings');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};