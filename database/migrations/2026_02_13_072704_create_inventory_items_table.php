<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('category', [
                'medicine',
                'vaccine',
                'supply',
                'food',
                'toy',
                'accessory',
                'other'
            ])->default('other');
            $table->string('sku', 50)->unique();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('sku');
            $table->index('is_active');
        });

        // Add check constraint
        DB::statement('ALTER TABLE inventory_items ADD CONSTRAINT chk_unit_price CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};