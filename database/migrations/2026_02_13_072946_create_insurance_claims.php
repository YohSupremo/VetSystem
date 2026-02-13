<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('insurance_provider', 150);
            $table->string('policy_number', 100);
            $table->string('claim_number', 100)->unique()->nullable();
            $table->decimal('claim_amount', 12, 2);
            $table->enum('claim_status', ['pending', 'submitted', 'approved', 'rejected', 'paid'])->default('pending');
            $table->date('submitted_date')->nullable();
            $table->date('processed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('claim_status');
            $table->index('claim_number');
        });

        // Add check constraint
        DB::statement('ALTER TABLE insurance_claims ADD CONSTRAINT chk_claim_amount CHECK (claim_amount > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};