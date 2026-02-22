<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('estimated_duration_minutes')->nullable(); // in minutes
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // Insert default surgery types
        DB::table('surgery_types')->insert([
            [
                'name' => 'Spay (Female)',
                'description' => 'Ovariohysterectomy - surgical sterilization of female animals',
                'price' => 2500.00,
                'estimated_duration_minutes' => 60,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Neuter (Male)',
                'description' => 'Castration - surgical sterilization of male animals',
                'price' => 2000.00,
                'estimated_duration_minutes' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dental Cleaning',
                'description' => 'Professional dental cleaning and polishing under anesthesia',
                'price' => 1500.00,
                'estimated_duration_minutes' => 90,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dental Extraction',
                'description' => 'Surgical removal of damaged or infected teeth',
                'price' => 2000.00,
                'estimated_duration_minutes' => 60,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tumor Removal',
                'description' => 'Surgical excision of benign or malignant tumors',
                'price' => 3500.00,
                'estimated_duration_minutes' => 120,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wound Repair',
                'description' => 'Surgical repair of lacerations or traumatic wounds',
                'price' => 1800.00,
                'estimated_duration_minutes' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Foreign Body Removal',
                'description' => 'Surgical removal of ingested or embedded foreign objects',
                'price' => 3000.00,
                'estimated_duration_minutes' => 90,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cesarean Section',
                'description' => 'Emergency or planned surgical delivery of offspring',
                'price' => 4500.00,
                'estimated_duration_minutes' => 120,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Orthopedic Surgery',
                'description' => 'Bone and joint surgical procedures (fractures, dislocations)',
                'price' => 5000.00,
                'estimated_duration_minutes' => 180,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Soft Tissue Surgery',
                'description' => 'General soft tissue surgical procedures',
                'price' => 3000.00,
                'estimated_duration_minutes' => 90,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Exploratory Laparotomy',
                'description' => 'Surgical examination of the abdominal cavity',
                'price' => 4000.00,
                'estimated_duration_minutes' => 120,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Eye Surgery',
                'description' => 'Ophthalmic surgical procedures (cataracts, glaucoma, etc.)',
                'price' => 3500.00,
                'estimated_duration_minutes' => 90,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_types');
    }
};
