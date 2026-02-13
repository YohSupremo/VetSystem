<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('inventory_stock')->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'adjustment', 'expired', 'damaged'])->default('out');
            $table->integer('quantity');
            $table->string('reference', 100)->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date')->useCurrent();

            $table->index('stock_id');
            $table->index('type');
            $table->index('transaction_date');
        });

        // Add check constraint
        DB::statement('ALTER TABLE inventory_transactions ADD CONSTRAINT chk_transaction_qty CHECK (quantity != 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};