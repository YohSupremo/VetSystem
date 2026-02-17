<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pet_vaccinations', function (Blueprint $table) {
            // Drop the vaccine_id foreign key
            $table->dropForeign('pet_vaccinations_vaccine_id_foreign');
            $table->dropColumn('vaccine_id');
            
            // Add inventory_item_id
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('pet_vaccinations', function (Blueprint $table) {
            $table->dropForeign('pet_vaccinations_inventory_item_id_foreign');
            $table->dropColumn('inventory_item_id');
            
            // Restore vaccine_id
            $table->foreignId('vaccine_id')->constrained()->onDelete('restrict');
        });
    }
};
