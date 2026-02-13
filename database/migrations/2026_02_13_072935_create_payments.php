<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->dateTime('payment_date')->useCurrent();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', [
                'cash',
                'credit_card',
                'debit_card',
                'bank_transfer',
                'check',
                'mobile_payment',
                'insurance',
                'other'
            ])->default('cash');
            $table->string('reference_number', 100)->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('invoice_id');
            $table->index('payment_date');
            $table->index('payment_method');
        });

        // Add check constraint
        DB::statement('ALTER TABLE payments ADD CONSTRAINT chk_payment_amount CHECK (amount > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};