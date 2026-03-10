<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('qr_scan_logs')) {
            return;
        }

        Schema::create('qr_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('scan_type', ['cage', 'pet']);
            $table->foreignId('cage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('scanned_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('scan_timestamp')->useCurrent();
            $table->string('location', 100)->nullable();
            $table->text('notes')->nullable();

            $table->index('cage_id');
            $table->index('pet_id');
            $table->index('scanned_by');
            $table->index('scan_timestamp');
            $table->index('scan_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scan_logs');
    }
};
