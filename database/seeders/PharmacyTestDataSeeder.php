<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PharmacyTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user if not exists
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'username' => 'testuser',
                'password' => bcrypt('password'),
                'address' => '123 Test Street',
                'contact_number' => '123-456-7890',
                'role' => 'admin',
                'is_active' => 1,
            ]
        );

        // Create pet owner
        $owner = \App\Models\PetOwner::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        );

        // Create test pet
        $pet = \App\Models\Pet::firstOrCreate(
            ['name' => 'Test Pet', 'owner_id' => $owner->id],
            [
                'owner_id' => $owner->id,
                'name' => 'Test Pet',
                'species' => 'Dog',
                'breed' => 'Labrador',
                'birth_date' => now()->subYears(3),
                'gender' => 'Male',
            ]
        );

        // Create medical record
        $record = \App\Models\MedicalRecord::firstOrCreate(
            ['pet_id' => $pet->id, 'visit_date' => now()],
            [
                'pet_id' => $pet->id,
                'veterinarian_id' => $user->id,
                'visit_date' => now(),
                'complaint' => 'Annual checkup and vaccination',
                'examination_notes' => 'Healthy dog, good condition',
                'diagnosis' => 'Healthy',
                'treatment_plan' => 'Vaccination administered',
            ]
        );

        // Update existing prescription to link to medical record
        $prescription = \App\Models\Prescription::first();
        if ($prescription) {
            $prescription->update([
                'medical_record_id' => $record->id,
                'dispensed' => false,
                'dispensed_at' => null,
            ]);
        }

        // Create test medicine
        $medicine = \App\Models\InventoryItem::firstOrCreate(
            ['name' => 'Test Vaccine', 'category' => 'medicine'],
            [
                'name' => 'Test Vaccine',
                'category' => 'medicine',
                'sku' => 'VAC001',
                'manufacturer' => 'Test Pharma',
                'unit_price' => 25.00,
                'requires_prescription' => true,
                'controlled_substance' => false,
                'description' => 'Annual vaccination for dogs',
            ]
        );

        // Create supplier
        $supplier = \App\Models\Supplier::firstOrCreate(
            ['supplier_name' => 'Test Supplier'],
            ['supplier_name' => 'Test Supplier']
        );

        // Create stock for the medicine
        \App\Models\InventoryStock::firstOrCreate(
            ['item_id' => $medicine->id, 'supplier_id' => $supplier->id],
            [
                'item_id' => $medicine->id,
                'supplier_id' => $supplier->id,
                'quantity' => 50,
                'min_stock' => 10,
                'expiry_date' => now()->addYears(2),
            ]
        );
    }
}
