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
            $table->text('description')->nullable()->after('name');
            $table->string('manufacturer', 150)->nullable()->after('description');
            $table->string('batch_number', 100)->nullable()->after('manufacturer');
            $table->string('dosage_form', 100)->nullable()->after('batch_number'); // tablet, capsule, liquid, etc.
            $table->string('strength', 100)->nullable()->after('dosage_form'); // 500mg, 10ml, etc.
            $table->boolean('requires_prescription')->default(false)->after('strength');
            $table->boolean('controlled_substance')->default(false)->after('requires_prescription');
            $table->text('storage_instructions')->nullable()->after('controlled_substance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'manufacturer',
                'batch_number',
                'dosage_form',
                'strength',
                'requires_prescription',
                'controlled_substance',
                'storage_instructions'
            ]);
        });
    }
};
