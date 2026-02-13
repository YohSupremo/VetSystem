<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('pet_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('owner_id')->constrained('pet_owners')->onDelete('cascade');
            
            // NEW: Invoice generation fields
            $table->string('invoice_prefix', 10)->default('INV')
                  ->comment('Invoice prefix (e.g., INV, NYC-INV for multi-location)');
            $table->unsignedInteger('invoice_sequence')
                  ->comment('Sequential number within year/prefix');
            
            $table->string('invoice_number', 50)->unique()
                  ->comment('Auto-generated: [prefix]-YYYY-NNNNNN (e.g., INV-2026-000001)');
            
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('tax_rate', 5, 2)->default(0.00)->comment('Tax percentage to apply');
            $table->decimal('discount_amount', 12, 2)->default(0.00)->comment('Manual discount amount');
            $table->string('discount_reason')->nullable()->comment('Reason for discount');
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('overdue_reminder_sent')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('owner_id');
            $table->index('invoice_number');
            $table->index('status');
            $table->index(['issue_date', 'due_date']);
            $table->index(['owner_id', 'status', 'due_date'], 'idx_invoice_search');
            $table->index(['invoice_prefix', 'invoice_sequence'], 'idx_invoice_generation');
        });

        // Add check constraints
        DB::statement('ALTER TABLE invoices ADD CONSTRAINT chk_invoice_amounts CHECK (tax_rate >= 0 AND tax_rate <= 100 AND discount_amount >= 0)');
        DB::statement('ALTER TABLE invoices ADD CONSTRAINT chk_invoice_dates CHECK (due_date >= issue_date)');
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};