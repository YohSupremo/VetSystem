<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('billing_invoices')->cascadeOnDelete();
            $table->dateTime('payment_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'check', 'insurance', 'online_payment', 'other']);
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_payments');
    }
};
