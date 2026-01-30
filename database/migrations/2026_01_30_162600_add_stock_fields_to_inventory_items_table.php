<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('unit_price')->constrained('suppliers');
            $table->integer('quantity')->default(0)->after('supplier_id');
            $table->integer('min_stock')->default(0)->after('quantity');
            $table->date('expiry_date')->nullable()->after('min_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'quantity', 'min_stock', 'expiry_date']);
        });
    }
};
