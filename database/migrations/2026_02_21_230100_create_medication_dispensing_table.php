<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_dispensing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('restrict');
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('quantity_dispensed');
            $table->decimal('unit_price', 10, 2);
            $table->dateTime('dispensed_at');
            $table->text('instructions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('prescription_id');
            $table->index('inventory_item_id');
            $table->index('dispensed_by');
            $table->index('dispensed_at');
            $table->index(['prescription_id', 'dispensed_at'], 'idx_dispensing_prescription_date');
        });

        DB::statement('ALTER TABLE medication_dispensing ADD CONSTRAINT chk_quantity_dispensed CHECK (quantity_dispensed > 0)');
        DB::statement('ALTER TABLE medication_dispensing ADD CONSTRAINT chk_dispensing_unit_price CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_dispensing');
    }
};
