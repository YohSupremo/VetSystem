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
use App\Models\Surgery;
use App\Models\Incident;
use App\Models\ChronicCondition;
use App\Models\PetAllergy;
use App\Models\CageAssignment;
use App\Models\Cage;
use App\Models\GroomingAppointment;
use App\Models\GroomingService;
use App\Models\LabRequisition;
use App\Models\LabTest;
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

        // Create random surgeries
        $surgeries = [
            [
                'date' => Carbon::now()->subMonths(10),
                'type' => 'Spay/Neuter',
                'outcome' => 'Successful, no complications',
                'status' => 'completed',
            ],
            [
                'date' => Carbon::now()->subMonths(4),
                'type' => 'Dental Surgery',
                'outcome' => 'Multiple teeth extracted, good recovery',
                'status' => 'completed',
            ],
        ];

        foreach ($surgeries as $surgeryData) {
            Surgery::create([
                'pet_id' => $pet->id,
                'surgeon_id' => $vetUser->id,
                'medical_record_id' => MedicalRecord::where('pet_id', $pet->id)->inRandomOrder()->first()?->id,
                'surgery_type_id' => 1, // Assuming surgery type exists
                'scheduled_date' => $surgeryData['date'],
                'anesthesia_type' => 'Isoflurane',
                'pre_op_notes' => 'Patient stable, no contraindications',
                'surgery_notes' => 'Procedure completed successfully',
                'post_op_instructions' => 'Monitor for 24 hours, restrict activity for 7 days',
                'outcome' => $surgeryData['outcome'],
                'status' => $surgeryData['status'],
                'created_at' => $surgeryData['date'],
            ]);
        }

        // Create random incidents
        $incidents = [
            [
                'date' => Carbon::now()->subMonths(7),
                'type' => 'medication_error',
                'severity' => 'minor',
                'description' => 'Incorrect dosage administered, corrected immediately',
                'status' => 'resolved',
            ],
            [
                'date' => Carbon::now()->subMonths(3),
                'type' => 'pet_escape',
                'severity' => 'moderate',
                'description' => 'Patient attempted to escape from cage during boarding',
                'status' => 'resolved',
            ],
            [
                'date' => Carbon::now()->subMonths(1),
                'type' => 'pet_illness',
                'severity' => 'severe',
                'description' => 'Severe allergic reaction to flea treatment',
                'status' => 'resolved',
            ],
        ];

        foreach ($incidents as $incidentData) {
            Incident::create([
                'incident_number' => 'INC-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'incident_date' => $incidentData['date'],
                'incident_type' => $incidentData['type'],
                'severity' => $incidentData['severity'],
                'pet_id' => $pet->id,
                'location' => 'Treatment Room ' . rand(1, 5),
                'description' => $incidentData['description'],
                'immediate_action_taken' => 'Patient monitored closely, vitals stable',
                'root_cause' => 'Human error in dosage calculation',
                'corrective_action' => 'Additional training provided to staff',
                'status' => $incidentData['status'],
                'resolved_date' => $incidentData['status'] === 'resolved' ? $incidentData['date']->copy()->addDays(rand(1, 7)) : null,
                'reported_by' => $vetUser->id,
                'reported_at' => $incidentData['date'],
                'created_at' => $incidentData['date'],
            ]);
        }

        // Create random chronic conditions
        $chronicConditions = [
            [
                'date' => Carbon::now()->subMonths(12),
                'condition' => 'Hip Dysplasia',
                'treatment' => 'Weight management, joint supplements, pain medication as needed',
                'notes' => 'Diagnosed during routine examination, good prognosis with management',
                'active' => true,
            ],
            [
                'date' => Carbon::now()->subMonths(6),
                'condition' => 'Seasonal Allergies',
                'treatment' => 'Antihistamine therapy during allergy season, environmental control',
                'notes' => 'Environmental allergies suspected, responds well to treatment',
                'active' => true,
            ],
        ];

        foreach ($chronicConditions as $conditionData) {
            ChronicCondition::create([
                'pet_id' => $pet->id,
                'condition_name' => $conditionData['condition'],
                'diagnosed_date' => $conditionData['date'],
                'ongoing_treatment' => $conditionData['treatment'],
                'notes' => $conditionData['notes'],
                'is_active' => $conditionData['active'],
                'created_at' => $conditionData['date'],
            ]);
        }

        // Create random allergies
        $allergies = [
            [
                'date' => Carbon::now()->subMonths(8),
                'allergen' => 'Grass Pollen',
                'reaction_type' => 'Environmental',
                'severity' => 'moderate',
                'notes' => 'Itching, redness, ear infections. Seasonal pattern observed.',
                'active' => true,
            ],
            [
                'date' => Carbon::now()->subMonths(5),
                'allergen' => 'Chicken Protein',
                'reaction_type' => 'Food',
                'severity' => 'severe',
                'notes' => 'Vomiting, diarrhea, facial swelling. Confirmed by elimination diet.',
                'active' => true,
            ],
            [
                'date' => Carbon::now()->subMonths(2),
                'allergen' => 'Flea Bites',
                'reaction_type' => 'Insect',
                'severity' => 'severe',
                'notes' => 'Intense itching, hot spots, secondary infections. Requires year-round prevention.',
                'active' => true,
            ],
        ];

        foreach ($allergies as $allergyData) {
            PetAllergy::create([
                'pet_id' => $pet->id,
                'allergen' => $allergyData['allergen'],
                'reaction_type' => $allergyData['reaction_type'],
                'severity' => $allergyData['severity'],
                'diagnosed_date' => $allergyData['date'],
                'notes' => $allergyData['notes'],
                'is_active' => $allergyData['active'],
                'created_at' => $allergyData['date'],
            ]);
        }

        // Create random cage assignments (hospitalization)
        $cageAssignments = [
            [
                'start' => Carbon::now()->subMonths(8),
                'end' => Carbon::now()->subMonths(8)->addDays(2),
                'notes' => 'Admitted for gastroenteritis treatment. IV fluids administered. Discharged when stable.',
                'feeding_schedule' => 'Small frequent meals',
                'medication_instructions' => 'Metronidazole 250mg twice daily',
            ],
            [
                'start' => Carbon::now()->subMonths(4),
                'end' => Carbon::now()->subMonths(4)->addDays(1),
                'notes' => 'Overnight stay for dental surgery recovery. Monitored closely.',
                'feeding_schedule' => 'Soft food only for 7 days post-op',
                'medication_instructions' => 'Carprofen 75mg twice daily for pain',
            ],
            [
                'start' => Carbon::now()->subDays(5),
                'end' => null, // Current/ongoing
                'notes' => 'Boarding while owner on vacation. Regular exercise and playtime provided.',
                'feeding_schedule' => 'Twice daily - morning and evening',
                'medication_instructions' => 'Heartgard Plus monthly',
            ],
        ];

        foreach ($cageAssignments as $assignmentData) {
            CageAssignment::create([
                'cage_id' => Cage::inRandomOrder()->first()?->id ?? 1,
                'pet_id' => $pet->id,
                'start_date' => $assignmentData['start'],
                'end_date' => $assignmentData['end'] ?? Carbon::now()->addDays(3), // Default end date if null
                'notes' => $assignmentData['notes'],
                'feeding_schedule' => $assignmentData['feeding_schedule'],
                'medication_instructions' => $assignmentData['medication_instructions'],
                'created_at' => $assignmentData['start'],
            ]);
        }

        // Create random grooming appointments
        $groomingServices = GroomingService::all();
        if ($groomingServices->isEmpty()) {
            // Create a basic grooming service if none exist
            GroomingService::create([
                'name' => 'Full Service Grooming',
                'description' => 'Complete grooming package',
                'duration_minutes' => 120,
                'price' => 75.00,
            ]);
            $groomingServices = GroomingService::all();
        }

        $groomingAppointments = [
            [
                'date' => Carbon::now()->subMonths(6),
                'service' => $groomingServices->random(),
                'status' => 'completed',
                'instructions' => 'Gentle handling, patient has allergies',
            ],
            [
                'date' => Carbon::now()->subMonths(3),
                'service' => $groomingServices->random(),
                'status' => 'completed',
                'instructions' => 'Focus on nail trimming, coat is healthy',
            ],
            [
                'date' => Carbon::now()->subMonths(1),
                'service' => $groomingServices->random(),
                'status' => 'completed',
                'instructions' => 'Regular maintenance grooming',
            ],
        ];

        foreach ($groomingAppointments as $appointmentData) {
            $appointment = Appointment::create([
                'pet_id' => $pet->id,
                'veterinarian_id' => $vetUser->id,
                'appointment_date' => $appointmentData['date'],
                'type' => 'grooming',
                'status' => $appointmentData['status'],
                'notes' => 'Grooming appointment',
                'created_at' => $appointmentData['date'],
            ]);

            GroomingAppointment::create([
                'appointment_id' => $appointment->id,
                'service_id' => $appointmentData['service']->id,
                'groomer_id' => $vetUser->id,
                'special_instructions' => $appointmentData['instructions'],
                'status' => $appointmentData['status'],
                'actual_duration_minutes' => $appointmentData['service']->duration_minutes,
                'created_at' => $appointmentData['date'],
            ]);
        }

        // Create random lab tests
        $labTests = LabTest::all();
        if ($labTests->isEmpty()) {
            // Create basic lab tests if none exist
            $testData = [
                ['name' => 'Complete Blood Count', 'description' => 'CBC - Full blood analysis', 'price' => 85.00],
                ['name' => 'Blood Chemistry Panel', 'description' => 'Comprehensive metabolic panel', 'price' => 120.00],
                ['name' => 'Urinalysis', 'description' => 'Urine analysis', 'price' => 45.00],
                ['name' => 'Fecal Exam', 'description' => 'Parasite examination', 'price' => 35.00],
                ['name' => 'Heartworm Test', 'description' => 'Heartworm antigen test', 'price' => 25.00],
            ];
            foreach ($testData as $test) {
                LabTest::create($test);
            }
            $labTests = LabTest::all();
        }

        $labRequisitions = [
            [
                'date' => Carbon::now()->subMonths(11),
                'test' => $labTests->random(),
                'status' => 'completed',
                'results' => 'All values within normal range',
            ],
            [
                'date' => Carbon::now()->subMonths(8),
                'test' => $labTests->where('name', 'Complete Blood Count')->first() ?? $labTests->random(),
                'status' => 'completed',
                'results' => 'Mild leukocytosis, otherwise normal',
            ],
            [
                'date' => Carbon::now()->subMonths(3),
                'test' => $labTests->where('name', 'Blood Chemistry Panel')->first() ?? $labTests->random(),
                'status' => 'completed',
                'results' => 'Elevated liver enzymes, monitor closely',
            ],
            [
                'date' => Carbon::now()->subMonths(1),
                'test' => $labTests->where('name', 'Heartworm Test')->first() ?? $labTests->random(),
                'status' => 'completed',
                'results' => 'Negative for heartworm antigen',
            ],
        ];

        foreach ($labRequisitions as $requisitionData) {
            $medicalRecord = MedicalRecord::where('pet_id', $pet->id)->inRandomOrder()->first();

            LabRequisition::create([
                'medical_record_id' => $medicalRecord?->id ?? MedicalRecord::where('pet_id', $pet->id)->first()->id,
                'test_id' => $requisitionData['test']->id,
                'requested_by' => $vetUser->id,
                'requested_date' => $requisitionData['date'],
                'sample_collected' => true,
                'sample_collection_date' => $requisitionData['date'],
                'status' => $requisitionData['status'],
                'results' => $requisitionData['results'],
                'result_date' => $requisitionData['date']->copy()->addDays(rand(1, 3)),
                'notes' => 'Results reviewed and discussed with owner',
                'result_notification_sent' => true,
                'created_at' => $requisitionData['date'],
            ]);
        }

        $this->command->info('Pet Reports Demo Data Seeded Successfully!');
        $this->command->info('Owner: john.smith@example.com / password123');
        $this->command->info('Pet: Max (Golden Retriever, 3 years old)');
        $this->command->info('Medical Records: 8 visits over past year');
        $this->command->info('Prescriptions: 6 medications');
        $this->command->info('Vaccinations: 8 immunization records');
        $this->command->info('Surgeries: 2 surgical procedures');
        $this->command->info('Incidents: 3 reported incidents');
        $this->command->info('Chronic Conditions: 2 ongoing conditions');
        $this->command->info('Allergies: 3 confirmed allergies');
        $this->command->info('Cage Assignments: 3 boarding/hospitalization periods');
        $this->command->info('Grooming Appointments: 3 grooming services');
        $this->command->info('Lab Tests: 4 laboratory requisitions');
    }
}
