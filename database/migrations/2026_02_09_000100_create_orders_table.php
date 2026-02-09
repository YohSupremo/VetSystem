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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained('pets')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('pet_owners')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('order_type', ['consultation', 'pharmacy', 'lab', 'grooming', 'boarding', 'product', 'other'])->default('product');
            $table->enum('status', ['draft', 'confirmed', 'fulfilled', 'cancelled'])->default('draft');
            $table->dateTime('order_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_type', 'status']);
            $table->index(['owner_id', 'order_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

