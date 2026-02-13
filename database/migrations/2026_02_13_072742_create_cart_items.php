<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('shopping_carts')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->comment('Price at time of adding to cart');
            $table->timestamps();

            $table->index('cart_id');
            $table->index('inventory_item_id');
        });

        // Add check constraints
        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT chk_cart_quantity CHECK (quantity > 0)');
        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT chk_cart_price CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};