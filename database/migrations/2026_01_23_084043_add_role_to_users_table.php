<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
        {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('user')->after('email');
                $table->string('first_name')->after('name');
                $table->string('last_name')->after('first_name');
            });

            // Update existing users to have a default role
            DB::table('users')->update(['role' => 'user']);
        }

        public function down()
        {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['role', 'first_name', 'last_name']);
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
