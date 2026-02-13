<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->text('note');
            $table->foreignId('added_by')->constrained('users')->onDelete('restrict');
            $table->dateTime('added_at')->useCurrent();

            $table->index('incident_id');
            $table->index('added_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_notes');
    }
};