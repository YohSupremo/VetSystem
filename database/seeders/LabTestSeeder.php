<?php

namespace Database\Seeders;

use App\Models\LabTest;
use Illuminate\Database\Seeder;

class LabTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tests = [
            [
                'test_name' => 'Complete Blood Count (CBC)',
                'category' => 'blood',
                'description' => 'Evaluates red cells, white cells, and platelets for infection, anemia, and inflammation.',
                'standard_price' => 850.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Blood Chemistry Panel',
                'category' => 'blood',
                'description' => 'Assesses liver, kidney, glucose, and electrolyte values.',
                'standard_price' => 1250.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Electrolyte Panel',
                'category' => 'blood',
                'description' => 'Measures sodium, potassium, chloride, and related balance markers.',
                'standard_price' => 700.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Urinalysis',
                'category' => 'urine',
                'description' => 'Screens urine for infection, crystals, glucose, and kidney-related abnormalities.',
                'standard_price' => 550.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Urine Culture and Sensitivity',
                'category' => 'urine',
                'description' => 'Identifies urinary pathogens and effective antibiotics.',
                'standard_price' => 1450.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Fecal Flotation',
                'category' => 'fecal',
                'description' => 'Detects intestinal parasite eggs and ova.',
                'standard_price' => 400.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Fecal Antigen Test (Giardia)',
                'category' => 'fecal',
                'description' => 'Rapid detection of Giardia antigen in stool samples.',
                'standard_price' => 650.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Fine Needle Aspiration Cytology',
                'category' => 'cytology',
                'description' => 'Cellular examination of masses, lymph nodes, or skin lesions.',
                'standard_price' => 1200.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Ear Cytology',
                'category' => 'cytology',
                'description' => 'Microscopic assessment for yeast, bacteria, and inflammatory cells.',
                'standard_price' => 450.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Skin Scraping Cytology',
                'category' => 'cytology',
                'description' => 'Evaluation for mites, fungal elements, and skin pathogens.',
                'standard_price' => 500.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Histopathology Biopsy',
                'category' => 'biopsy',
                'description' => 'Tissue pathology for definitive diagnosis of tumors and chronic disease.',
                'standard_price' => 3200.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Ultrasound-Guided Biopsy',
                'category' => 'biopsy',
                'description' => 'Targeted tissue sampling using ultrasound guidance.',
                'standard_price' => 4500.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Thoracic X-Ray (2 Views)',
                'category' => 'imaging',
                'description' => 'Chest radiographs to evaluate lungs, heart, and thoracic cavity.',
                'standard_price' => 1800.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Abdominal X-Ray (2 Views)',
                'category' => 'imaging',
                'description' => 'Abdominal radiographs for GI, urinary, and organ assessment.',
                'standard_price' => 1800.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Abdominal Ultrasound',
                'category' => 'imaging',
                'description' => 'Ultrasound scan of abdominal organs and fluid spaces.',
                'standard_price' => 2500.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Heartworm Antigen Test',
                'category' => 'other',
                'description' => 'Screening test for canine heartworm infection.',
                'standard_price' => 950.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Canine Parvovirus Rapid Test',
                'category' => 'other',
                'description' => 'Rapid antigen detection for canine parvovirus.',
                'standard_price' => 850.00,
                'is_active' => true,
            ],
            [
                'test_name' => 'Feline Leukemia/FIV Rapid Test',
                'category' => 'other',
                'description' => 'Combo screening for FeLV antigen and FIV antibodies.',
                'standard_price' => 1100.00,
                'is_active' => true,
            ],
        ];

        foreach ($tests as $test) {
            LabTest::updateOrCreate(
                ['test_name' => $test['test_name']],
                $test
            );
        }
    }
}
