<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('restrict');
            $table->string('description')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->comment('Price at time of order');
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id');
            $table->index('inventory_item_id');
        });

        // Add check constraints
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_item_qty CHECK (quantity > 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_item_price CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};