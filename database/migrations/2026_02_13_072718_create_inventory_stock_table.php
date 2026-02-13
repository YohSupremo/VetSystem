<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('min_stock')->default(10);
            $table->integer('max_stock')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('location', 100)->nullable();
            $table->boolean('low_stock_alert_sent')->default(false);
            $table->boolean('expiry_alert_sent')->default(false);
            $table->timestamps();

            $table->index('item_id');
            $table->index('quantity');
            $table->index('expiry_date');
            $table->index(['item_id', 'quantity', 'expiry_date'], 'idx_inventory_search');
        });

        // Add check constraints
        DB::statement('ALTER TABLE inventory_stock ADD CONSTRAINT chk_quantity CHECK (quantity >= 0)');
        DB::statement('ALTER TABLE inventory_stock ADD CONSTRAINT chk_min_stock CHECK (min_stock >= 0)');
        DB::statement('ALTER TABLE inventory_stock ADD CONSTRAINT chk_max_stock CHECK (max_stock IS NULL OR max_stock >= min_stock)');
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock');
    }
};