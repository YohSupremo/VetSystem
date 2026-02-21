<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medication_dispensing', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->after('prescription_id')->constrained('invoices')->onDelete('set null');
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::table('medication_dispensing', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_id');
        });
    }
};
