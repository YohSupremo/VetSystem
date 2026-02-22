<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surgeries', function (Blueprint $table) {
            // Drop the old procedure_name column
            $table->dropColumn('procedure_name');
            
            // Add surgery_type_id foreign key
            $table->foreignId('surgery_type_id')->after('surgeon_id')->constrained('surgery_types')->onDelete('restrict');
            
            // Add index for surgery_type_id
            $table->index('surgery_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('surgeries', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['surgery_type_id']);
            $table->dropIndex(['surgery_type_id']);
            $table->dropColumn('surgery_type_id');
            
            // Restore the procedure_name column
            $table->string('procedure_name')->after('scheduled_date');
        });
    }
};
