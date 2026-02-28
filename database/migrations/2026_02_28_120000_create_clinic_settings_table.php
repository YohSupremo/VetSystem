<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name', 150);
            $table->string('clinic_phone', 30)->nullable();
            $table->string('clinic_email', 150)->nullable();
            $table->text('clinic_address')->nullable();
            $table->string('timezone', 100)->default('Asia/Manila');
            $table->char('currency_code', 3)->default('PHP');
            $table->string('invoice_prefix', 10)->default('INV');
            $table->decimal('default_tax_rate', 5, 2)->default(0.00);
            $table->integer('appointment_slot_minutes')->default(30);
            $table->integer('appointment_buffer_minutes')->default(10);
            $table->integer('low_stock_threshold')->default(10);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('clinic_name', 'idx_clinic_name');
            $table->index('invoice_prefix', 'idx_invoice_prefix');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};
