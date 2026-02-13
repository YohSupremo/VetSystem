<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->enum('item_type', [
                'service',
                'consultation',
                'grooming',
                'boarding',
                'surgery',
                'lab_test',
                'vaccination',
                'product',
                'other'
            ])->default('service');
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->timestamp('created_at')->useCurrent();

            $table->index('invoice_id');
            $table->index('item_type');
        });

        // Add check constraints
        DB::statement('ALTER TABLE invoice_items ADD CONSTRAINT chk_invoice_item_qty CHECK (quantity > 0)');
        DB::statement('ALTER TABLE invoice_items ADD CONSTRAINT chk_invoice_item_price CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};