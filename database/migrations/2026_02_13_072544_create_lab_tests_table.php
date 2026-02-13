<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name', 150);
            $table->enum('category', [
                'blood',
                'urine',
                'fecal',
                'biopsy',
                'cytology',
                'imaging',
                'other'
            ])->default('other');
            $table->text('description')->nullable();
            $table->decimal('standard_price', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });

        // Add check constraint
        DB::statement('ALTER TABLE lab_tests ADD CONSTRAINT chk_price CHECK (standard_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};