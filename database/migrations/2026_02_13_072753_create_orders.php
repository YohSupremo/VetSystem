<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('pet_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('owner_id')->constrained('pet_owners')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->enum('order_type', ['walk_in', 'appointment', 'online', 'pharmacy'])->default('walk_in');
            $table->enum('status', ['draft', 'confirmed', 'shipped', 'fulfilled', 'cancelled'])->default('draft');
            $table->dateTime('order_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('owner_id');
            $table->index('pet_id');
            $table->index('status');
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};