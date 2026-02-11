<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medications = [
            [
                'name' => 'Amoxicillin',
                'description' => 'Broad-spectrum antibiotic for bacterial infections',
                'strength' => '250mg',
                'form' => 'Capsule',
                'category' => 'Antibiotic',
                'manufacturer' => 'VetPharma',
                'requires_prescription' => true,
                'side_effects' => 'Nausea, diarrhea, allergic reactions',
                'dosage_instructions' => 'Give with food, twice daily',
                'contraindications' => 'Known penicillin allergy',
                'storage_requirements' => 'Store at room temperature',
                'is_active' => true,
            ],
            [
                'name' => 'Prednisone',
                'description' => 'Anti-inflammatory steroid for allergies and inflammation',
                'strength' => '5mg',
                'form' => 'Tablet',
                'category' => 'Steroid',
                'manufacturer' => 'MediVet',
                'requires_prescription' => true,
                'side_effects' => 'Increased thirst, urination, appetite changes',
                'dosage_instructions' => 'Give with food to reduce stomach upset',
                'contraindications' => 'Diabetes, infections, pregnancy',
                'storage_requirements' => 'Store at room temperature',
                'is_active' => true,
            ],
            [
                'name' => 'Rimadyl (Carprofen)',
                'description' => 'NSAID for pain and inflammation in dogs',
                'strength' => '75mg',
                'form' => 'Chewable Tablet',
                'category' => 'NSAID',
                'manufacturer' => 'Zoetis',
                'requires_prescription' => true,
                'side_effects' => 'Stomach upset, kidney effects',
                'dosage_instructions' => 'Give with food, once daily',
                'contraindications' => 'Kidney disease, stomach ulcers',
                'storage_requirements' => 'Store at room temperature',
                'is_active' => true,
            ],
            [
                'name' => 'Heartgard Plus',
                'description' => 'Monthly heartworm prevention',
                'strength' => '68mcg/57mg',
                'form' => 'Chewable',
                'category' => 'Preventative',
                'manufacturer' => 'Boehringer Ingelheim',
                'requires_prescription' => true,
                'side_effects' => 'Rare: vomiting, diarrhea',
                'dosage_instructions' => 'Give monthly, year-round',
                'contraindications' => 'Puppies under 6 weeks',
                'storage_requirements' => 'Store at room temperature',
                'is_active' => true,
            ],
            [
                'name' => 'Apoquel',
                'description' => 'Anti-itch medication for allergic dermatitis',
                'strength' => '5.4mg',
                'form' => 'Tablet',
                'category' => 'Antihistamine',
                'manufacturer' => 'Zoetis',
                'requires_prescription' => true,
                'side_effects' => 'Vomiting, diarrhea, appetite loss',
                'dosage_instructions' => 'Give twice daily as needed',
                'contraindications' => 'Serious infections, cancer',
                'storage_requirements' => 'Store at room temperature',
                'is_active' => true,
            ],
        ];

        foreach ($medications as $medication) {
            Medication::create($medication);
        }
    }
}
