<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pet_vaccinations', function (Blueprint $table) {
            // Add fields for veterinarian-created vaccinations
            $table->string('vaccine_name', 200)->nullable()->after('inventory_item_id');
            $table->string('vaccine_type', 100)->nullable()->after('vaccine_name');
            $table->string('manufacturer', 200)->nullable()->after('vaccine_type');
            $table->foreignId('veterinarian_id')->nullable()->constrained('users')->onDelete('restrict')->after('administered_by');
            $table->enum('status', ['administered', 'pending', 'cancelled'])->default('administered')->after('reminder_sent');
            $table->dateTime('vaccination_date')->nullable()->after('status');
            $table->timestamp('updated_at')->nullable()->after('vaccination_date');
        });
    }

    public function down(): void
    {
        Schema::table('pet_vaccinations', function (Blueprint $table) {
            $table->dropForeignKey('pet_vaccinations_veterinarian_id_foreign');
            $table->dropColumn([
                'vaccine_name',
                'vaccine_type',
                'manufacturer',
                'veterinarian_id',
                'status',
                'vaccination_date',
                'updated_at'
            ]);
        });
    }
};
