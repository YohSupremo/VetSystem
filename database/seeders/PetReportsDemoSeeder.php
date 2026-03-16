<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PetOwner;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Medication;
use App\Models\PetVaccination;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PetReportsDemoSeeder extends Seeder
{
    public function run()
    {
        // Create a pet owner user
        $ownerUser = User::firstOrCreate(
            ['username' => 'johnsmith'],
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'password' => bcrypt('password123'),
                'role' => 'pet_owner',
                'email_verified' => true,
            ]
        );

        // Create pet owner profile if it doesn't exist
        if (!$ownerUser->petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $ownerUser->id,
                'emergency_contact_name' => 'Jane Smith',
                'emergency_contact_phone' => '+1-555-0124',
                'emergency_contact_relationship' => 'Spouse',
            ]);
        } else {
            $petOwner = $ownerUser->petOwner;
        }

        // Create a veterinarian for the records
        $vetUser = User::firstOrCreate(
            ['username' => 'drjohnson'],
            [
                'first_name' => 'Dr. Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@vetclinic.com',
                'password' => bcrypt('password123'),
                'role' => 'veterinarian',
                'email_verified' => true,
            ]
        );

        // Create the demo pet (check if it already exists)
        $pet = Pet::firstOrCreate(
            ['name' => 'Max', 'owner_id' => $petOwner->id],
            [
                'species' => 'Dog',
                'breed' => 'Golden Retriever',
                'birth_date' => Carbon::now()->subYears(3)->subMonths(2), // 3 years 2 months old
                'gender' => 'Male',
                'color' => 'Golden',
                'weight' => 32.5,
                'registration_number' => 'REG-2021-001',
                'is_active' => true,
            ]
        );

        // Create medications for prescriptions
        $medications = [
            ['name' => 'Amoxicillin', 'description' => 'Antibiotic for bacterial infections'],
            ['name' => 'Carprofen', 'description' => 'Anti-inflammatory for pain relief'],
            ['name' => 'Heartgard Plus', 'description' => 'Heartworm prevention'],
            ['name' => 'Frontline Plus', 'description' => 'Flea and tick prevention'],
            ['name' => 'Apoquel', 'description' => 'Allergy relief medication'],
        ];

        foreach ($medications as $med) {
            Medication::create($med);
        }

        // Create appointments over the past year
        $appointments = [
            [
                'date' => Carbon::now()->subMonths(11),
                'type' => 'Annual Checkup',
                'status' => 'completed',
                'notes' => 'Regular annual wellness examination',
            ],
            [
                'date' => Carbon::now()->subMonths(9),
                'type' => 'Vaccination',
                'status' => 'completed',
                'notes' => 'DHPP booster and rabies vaccination',
            ],
            [
                'date' => Carbon::now()->subMonths(8),
                'type' => 'Sick Visit',
                'status' => 'completed',
                'notes' => 'Presenting with vomiting and lethargy',
            ],
            [
                'date' => Carbon::now()->subMonths(6),
                'type' => 'Follow-up',
                'status' => 'completed',
                'notes' => 'Post-illness checkup, recovery progressing well',
            ],
            [
                'date' => Carbon::now()->subMonths(5),
                'type' => 'Dental Cleaning',
                'status' => 'completed',
                'notes' => 'Professional dental cleaning under anesthesia',
            ],
            [
                'date' => Carbon::now()->subMonths(3),
                'type' => 'Skin Issues',
                'status' => 'completed',
                'notes' => 'Allergic dermatitis, itching and redness',
            ],
            [
                'date' => Carbon::now()->subMonths(2),
                'type' => 'Injury',
                'status' => 'completed',
                'notes' => 'Limping on left hind leg, possible sprain',
            ],
            [
                'date' => Carbon::now()->subMonths(1),
                'type' => 'Wellness Check',
                'status' => 'completed',
                'notes' => 'Pre-travel health certificate examination',
            ],
        ];

        foreach ($appointments as $appointmentData) {
            Appointment::create([
                'pet_id' => $pet->id,
                'veterinarian_id' => $vetUser->id,
                'appointment_date' => $appointmentData['date'],
                'type' => $appointmentData['type'] === 'Annual Checkup' ? 'consultation' : 
                         ($appointmentData['type'] === 'Vaccination' ? 'vaccination' : 
                         ($appointmentData['type'] === 'Sick Visit' ? 'emergency' : 'consultation')),
                'status' => $appointmentData['status'],
                'notes' => $appointmentData['notes'],
                'created_at' => $appointmentData['date'],
                'updated_at' => $appointmentData['date'],
            ]);
        }

        // Create medical records with varied diagnoses and treatments
        $medicalRecords = [
            [
                'date' => Carbon::now()->subMonths(11),
                'diagnosis' => 'Healthy - No abnormalities detected',
                'treatment_plan' => 'Continue regular preventive care, return in 6 months',
                'notes' => 'Patient appears healthy, all vitals normal',
            ],
            [
                'date' => Carbon::now()->subMonths(9),
                'diagnosis' => 'Routine vaccination visit',
                'treatment_plan' => 'DHPP booster and rabies vaccine administered',
                'notes' => 'No adverse reactions observed',
            ],
            [
                'date' => Carbon::now()->subMonths(8),
                'diagnosis' => 'Acute gastroenteritis',
                'treatment_plan' => 'Fluid therapy, anti-nausea medication, temporary diet change',
                'notes' => 'Patient dehydrated, responded well to treatment',
            ],
            [
                'date' => Carbon::now()->subMonths(6),
                'diagnosis' => 'Post-illness recovery check',
                'treatment_plan' => 'Continue normal diet, monitor for recurrence',
                'notes' => 'Full recovery confirmed, no ongoing issues',
            ],
            [
                'date' => Carbon::now()->subMonths(5),
                'diagnosis' => 'Mild dental tartar buildup',
                'treatment_plan' => 'Professional dental cleaning performed',
                'notes' => 'Grade 2 dental disease, cleaning successful',
            ],
            [
                'date' => Carbon::now()->subMonths(3),
                'diagnosis' => 'Allergic dermatitis',
                'treatment_plan' => 'Antihistamine therapy, medicated shampoo, dietary trial',
                'notes' => 'Environmental allergies suspected, responding to treatment',
            ],
            [
                'date' => Carbon::now()->subMonths(2),
                'diagnosis' => 'Soft tissue injury - left hind leg',
                'treatment_plan' => 'Rest, anti-inflammatory medication, limited activity',
                'notes' => 'Sprain suspected, no fractures on x-ray',
            ],
            [
                'date' => Carbon::now()->subMonths(1),
                'diagnosis' => 'Healthy - Travel clearance',
                'treatment_plan' => 'Health certificate issued, all vaccinations current',
                'notes' => 'Fit for travel, no health restrictions',
            ],
        ];

        foreach ($medicalRecords as $recordData) {
            MedicalRecord::create([
                'pet_id' => $pet->id,
                'veterinarian_id' => $vetUser->id,
                'visit_date' => $recordData['date'],
                'diagnosis' => $recordData['diagnosis'],
                'treatment_plan' => $recordData['treatment_plan'],
                'examination_notes' => $recordData['notes'],
                'created_at' => $recordData['date'],
                'updated_at' => $recordData['date'],
            ]);
        }

        // Create prescriptions linked to medical records
        $prescriptions = [
            [
                'medical_record_date' => Carbon::now()->subMonths(8),
                'medication_name' => 'Amoxicillin',
                'dosage' => '250mg twice daily for 7 days',
                'instructions' => 'Give with food to reduce stomach upset',
                'status' => 'completed',
            ],
            [
                'medical_record_date' => Carbon::now()->subMonths(8),
                'medication_name' => 'Maropitant',
                'dosage' => '1mg/kg once daily for 3 days',
                'instructions' => 'Anti-nausea medication, give 30 minutes before meals',
                'status' => 'completed',
            ],
            [
                'medical_record_date' => Carbon::now()->subMonths(3),
                'medication_name' => 'Apoquel',
                'dosage' => '16mg twice daily',
                'instructions' => 'Continue for 14 days, then reassess',
                'status' => 'completed',
            ],
            [
                'medical_record_date' => Carbon::now()->subMonths(2),
                'medication_name' => 'Carprofen',
                'dosage' => '75mg once daily for 5 days',
                'instructions' => 'Give with food, monitor for stomach upset',
                'status' => 'completed',
            ],
            [
                'medical_record_date' => Carbon::now()->subMonths(1),
                'medication_name' => 'Heartgard Plus',
                'dosage' => 'One chewable monthly',
                'instructions' => 'Give on the same day each month, year-round prevention',
                'status' => 'active',
            ],
            [
                'medical_record_date' => Carbon::now()->subMonths(1),
                'medication_name' => 'Frontline Plus',
                'dosage' => 'Apply monthly between shoulder blades',
                'instructions' => 'Flea and tick prevention, avoid bathing for 48 hours after application',
                'status' => 'active',
            ],
        ];

        foreach ($prescriptions as $prescriptionData) {
            $medicalRecord = MedicalRecord::where('pet_id', $pet->id)
                ->where('visit_date', $prescriptionData['medical_record_date'])
                ->first();

            if ($medicalRecord) {
                Prescription::create([
                    'medical_record_id' => $medicalRecord->id,
                    'medication_id' => Medication::where('name', $prescriptionData['medication_name'])->first()?->id,
                    'dosage' => $prescriptionData['dosage'],
                    'instructions' => $prescriptionData['instructions'],
                    'status' => $prescriptionData['status'],
                    'start_date' => $prescriptionData['medical_record_date'],
                    'end_date' => $prescriptionData['status'] === 'active' ? null : $prescriptionData['medical_record_date']->copy()->addDays(14),
                    'created_at' => $prescriptionData['medical_record_date'],
                    'updated_at' => $prescriptionData['medical_record_date'],
                ]);
            }
        }

        // Create vaccination records
        $vaccinations = [
            [
                'date' => Carbon::now()->subYears(2)->subMonths(10),
                'vaccine' => 'DHPP',
                'next_due' => Carbon::now()->subMonths(9),
                'notes' => 'Puppy series vaccination',
            ],
            [
                'date' => Carbon::now()->subYears(2)->subMonths(7),
                'vaccine' => 'DHPP',
                'next_due' => Carbon::now()->subMonths(9),
                'notes' => 'Puppy series booster',
            ],
            [
                'date' => Carbon::now()->subYears(2)->subMonths(4),
                'vaccine' => 'DHPP',
                'next_due' => Carbon::now()->subMonths(9),
                'notes' => 'Final puppy vaccination',
            ],
            [
                'date' => Carbon::now()->subYears(1)->subMonths(10),
                'vaccine' => 'DHPP',
                'next_due' => Carbon::now()->subMonths(9),
                'notes' => 'Annual booster',
            ],
            [
                'date' => Carbon::now()->subMonths(9),
                'vaccine' => 'DHPP',
                'next_due' => Carbon::now()->addMonths(3),
                'notes' => 'Annual booster administered',
            ],
            [
                'date' => Carbon::now()->subYears(2)->subMonths(10),
                'vaccine' => 'Rabies',
                'next_due' => Carbon::now()->subMonths(11),
                'notes' => 'Initial rabies vaccination',
            ],
            [
                'date' => Carbon::now()->subMonths(11),
                'vaccine' => 'Rabies',
                'next_due' => Carbon::now()->addMonths(1),
                'notes' => '3-year rabies vaccine administered',
            ],
            [
                'date' => Carbon::now()->subMonths(1),
                'vaccine' => 'Bordetella',
                'next_due' => Carbon::now()->addMonths(5),
                'notes' => 'Kennel cough vaccine for boarding',
            ],
        ];

        foreach ($vaccinations as $vaccinationData) {
            PetVaccination::create([
                'pet_id' => $pet->id,
                'administered_by' => $vetUser->id,
                'batch_number' => 'BATCH-' . strtoupper(Str::random(8)),
                'administered_date' => $vaccinationData['date'],
                'next_due_date' => $vaccinationData['next_due'],
                'notes' => $vaccinationData['vaccine'] . ' - ' . $vaccinationData['notes'],
                'created_at' => $vaccinationData['date'],
            ]);
        }

        $this->command->info('Pet Reports Demo Data Seeded Successfully!');
        $this->command->info('Owner: john.smith@example.com / password123');
        $this->command->info('Pet: Max (Golden Retriever, 3 years old)');
        $this->command->info('Medical Records: 8 visits over past year');
        $this->command->info('Prescriptions: 6 medications');
        $this->command->info('Vaccinations: 8 immunization records');
    }
}
