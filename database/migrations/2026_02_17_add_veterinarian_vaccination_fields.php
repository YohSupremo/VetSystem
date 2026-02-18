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
            $table->string('vaccine_name', 200)->nullable();
            $table->string('vaccine_type', 100)->nullable();
            $table->string('manufacturer', 200)->nullable();
            $table->foreignId('veterinarian_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->enum('status', ['administered', 'pending', 'cancelled'])->default('administered');
            $table->dateTime('vaccination_date')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pet_vaccinations', function (Blueprint $table) {
            $table->dropForeign('pet_vaccinations_veterinarian_id_foreign');
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
