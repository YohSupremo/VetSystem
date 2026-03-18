<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Cage;
use App\Models\CageAssignment;
use App\Models\ChronicCondition;
use App\Models\GroomingAppointment;
use App\Models\GroomingService;
use App\Models\Incident;
use App\Models\LabRequisition;
use App\Models\LabTest;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\PetAllergy;
use App\Models\PetOwner;
use App\Models\PetVaccination;
use App\Models\Prescription;
use App\Models\Surgery;
use App\Models\SurgeryType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PetReportsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $users = $this->ensureSupportUsers();
        $surgeryTypes = $this->ensureSurgeryTypes();
        $groomingServices = $this->ensureGroomingServices();
        $labTests = $this->ensureLabTests();
        $cages = $this->ensureCages();

        $pets = Pet::with('owner.user')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $requestedCount = (int) env('PET_REPORTS_SEED_TARGET', 60);
        $targetCount = max($pets->count(), max($requestedCount, 6));

        while ($pets->count() < $targetCount) {
            $pets->push($this->createRandomPetWithOwner($faker, $pets->count() + 1));
        }

        foreach ($pets as $pet) {
            $this->seedPetReportData(
                $pet,
                $faker,
                $users,
                $surgeryTypes,
                $groomingServices,
                $labTests,
                $cages
            );
        }
    }

    private function ensureSupportUsers(): array
    {
        $vet = User::updateOrCreate(
            ['username' => 'report_vet'],
            [
                'first_name' => 'Avery',
                'last_name' => 'Lopez',
                'email' => 'report.vet@vetclinic.test',
                'password' => Hash::make('PawCare123'),
                'role' => 'veterinarian',
                'contact_number' => '09171110001',
                'address' => 'Clinic Floor 2',
                'is_active' => true,
                'email_verified' => true,
                'phone_verified' => true,
            ]
        );

        $groomer = User::updateOrCreate(
            ['username' => 'report_groomer'],
            [
                'first_name' => 'Jamie',
                'last_name' => 'Cruz',
                'email' => 'report.groomer@vetclinic.test',
                'password' => Hash::make('PawCare123'),
                'role' => 'groomer',
                'contact_number' => '09171110002',
                'address' => 'Grooming Room',
                'is_active' => true,
                'email_verified' => true,
                'phone_verified' => true,
            ]
        );

        $staff = User::updateOrCreate(
            ['username' => 'report_staff'],
            [
                'first_name' => 'Taylor',
                'last_name' => 'Reyes',
                'email' => 'report.staff@vetclinic.test',
                'password' => Hash::make('PawCare123'),
                'role' => 'staff',
                'contact_number' => '09171110003',
                'address' => 'Nursing Station',
                'is_active' => true,
                'email_verified' => true,
                'phone_verified' => true,
            ]
        );

        return [
            'vet' => $vet,
            'groomer' => $groomer,
            'staff' => $staff,
        ];
    }

    private function ensureSurgeryTypes(): Collection
    {
        $defaults = [
            ['name' => 'Dental Cleaning', 'description' => 'Routine dental cleaning under anesthesia.', 'price' => 1500, 'estimated_duration_minutes' => 90],
            ['name' => 'Tumor Removal', 'description' => 'Surgical excision of a mass for histopathology.', 'price' => 3500, 'estimated_duration_minutes' => 120],
            ['name' => 'Wound Repair', 'description' => 'Closure and repair of traumatic wounds.', 'price' => 1800, 'estimated_duration_minutes' => 60],
        ];

        foreach ($defaults as $type) {
            SurgeryType::firstOrCreate(
                ['name' => $type['name']],
                $type + ['is_active' => true]
            );
        }

        return SurgeryType::query()->where('is_active', true)->get();
    }

    private function ensureGroomingServices(): Collection
    {
        $defaults = [
            ['service_name' => 'Basic Bath', 'description' => 'Bath and blow dry.', 'duration_minutes' => 45, 'price' => 600],
            ['service_name' => 'Full Groom', 'description' => 'Bath, trim, nails, and ear care.', 'duration_minutes' => 90, 'price' => 1200],
            ['service_name' => 'Nail Trim', 'description' => 'Quick nail care session.', 'duration_minutes' => 20, 'price' => 250],
        ];

        foreach ($defaults as $service) {
            GroomingService::firstOrCreate(
                ['service_name' => $service['service_name']],
                $service + ['is_active' => true]
            );
        }

        return GroomingService::query()->where('is_active', true)->get();
    }

    private function ensureLabTests(): Collection
    {
        $defaults = [
            ['test_name' => 'Complete Blood Count (CBC)', 'category' => 'blood', 'description' => 'Screens red cells, white cells, and platelets.', 'standard_price' => 850],
            ['test_name' => 'Urinalysis', 'category' => 'urine', 'description' => 'General urine screening for infection and kidney issues.', 'standard_price' => 550],
            ['test_name' => 'Fecal Flotation', 'category' => 'fecal', 'description' => 'Checks for intestinal parasites.', 'standard_price' => 400],
        ];

        foreach ($defaults as $test) {
            LabTest::firstOrCreate(
                ['test_name' => $test['test_name']],
                $test + ['is_active' => true]
            );
        }

        return LabTest::query()->where('is_active', true)->get();
    }

    private function ensureCages(): Collection
    {
        $defaults = [
            ['cage_code' => 'PR-01', 'location' => 'Pet Report Ward', 'size' => 'medium', 'status' => 'available'],
            ['cage_code' => 'PR-02', 'location' => 'Pet Report Ward', 'size' => 'large', 'status' => 'available'],
        ];

        foreach ($defaults as $cage) {
            Cage::firstOrCreate(['cage_code' => $cage['cage_code']], $cage);
        }

        return Cage::query()->whereNotIn('status', ['maintenance', 'out_of_service'])->get();
    }

    private function createRandomPetWithOwner($faker, int $sequence): Pet
    {
        $token = Str::lower(Str::random(8));

        $ownerUser = User::create([
            'username' => "report_owner_{$token}",
            'email' => "report.owner.{$token}@vetclinic.test",
            'password' => Hash::make('PawCare123'),
            'role' => 'pet_owner',
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'contact_number' => '09' . $faker->numerify('#########'),
            'address' => $faker->address(),
            'is_active' => true,
            'email_verified' => true,
            'phone_verified' => true,
        ]);

        $owner = PetOwner::create([
            'user_id' => $ownerUser->id,
            'notes' => 'Generated owner profile for pet report demo data.',
            'preferred_contact_method' => $faker->randomElement(['email', 'sms']),
            'emergency_contact_name' => $faker->name(),
            'emergency_contact_phone' => '09' . $faker->numerify('#########'),
            'emergency_contact_relationship' => $faker->randomElement(['Sibling', 'Parent', 'Spouse', 'Friend']),
        ]);

        $species = $faker->randomElement(['Dog', 'Cat', 'Rabbit']);
        $breed = match ($species) {
            'Dog' => $faker->randomElement(['Golden Retriever', 'Shih Tzu', 'Labrador Retriever', 'Pomeranian']),
            'Cat' => $faker->randomElement(['Persian', 'Siamese', 'Domestic Shorthair', 'Maine Coon']),
            default => $faker->randomElement(['Holland Lop', 'Mini Rex', 'Lionhead']),
        };

        return Pet::create([
            'owner_id' => $owner->id,
            'name' => $faker->unique()->firstName() . ' ' . $sequence,
            'species' => $species,
            'breed' => $breed,
            'birth_date' => Carbon::now()->subMonths($faker->numberBetween(8, 120))->toDateString(),
            'gender' => $faker->randomElement(['male', 'female']),
            'color' => $faker->safeColorName(),
            'weight' => $faker->randomFloat(2, 2.5, 38),
            'microchip_number' => $faker->boolean(70) ? '985' . $faker->unique()->numerify('############') : null,
            'is_active' => true,
        ]);
    }

    private function seedPetReportData(
        Pet $pet,
        $faker,
        array $users,
        Collection $surgeryTypes,
        Collection $groomingServices,
        Collection $labTests,
        Collection $cages
    ): void {
        $appointments = Appointment::where('pet_id', $pet->id)->orderBy('appointment_date')->get();

        if ($appointments->isEmpty()) {
            $appointments = $this->createAppointments($pet, $faker, $users['vet']);
        }

        $medicalRecords = MedicalRecord::where('pet_id', $pet->id)->orderBy('visit_date')->get();

        if ($medicalRecords->isEmpty()) {
            $medicalRecords = $this->createMedicalRecords($pet, $appointments, $faker, $users['vet']);
        }

        if (! Prescription::whereHas('medicalRecord', function ($query) use ($pet) {
            $query->where('pet_id', $pet->id);
        })->exists()) {
            $this->createPrescriptions($medicalRecords, $faker, $users['staff']);
        }

        if (! PetVaccination::where('pet_id', $pet->id)->exists()) {
            $this->createVaccinations($pet, $faker, $users['vet']);
        }

        if (! Surgery::where('pet_id', $pet->id)->exists()) {
            $this->createSurgeries($pet, $medicalRecords, $faker, $users['vet'], $surgeryTypes);
        }

        if (! Incident::where('pet_id', $pet->id)->exists()) {
            $this->createIncidents($pet, $faker, $users['staff'], $cages);
        }

        if (! ChronicCondition::where('pet_id', $pet->id)->exists()) {
            $this->createChronicCondition($pet, $faker, $medicalRecords);
        }

        if (! PetAllergy::where('pet_id', $pet->id)->exists()) {
            $this->createAllergy($pet, $faker, $medicalRecords);
        }

        if (! CageAssignment::where('pet_id', $pet->id)->exists()) {
            $this->createCageAssignment($pet, $faker, $cages);
        }

        if (! GroomingAppointment::whereHas('appointment', function ($query) use ($pet) {
            $query->where('pet_id', $pet->id);
        })->exists()) {
            $this->createGroomingAppointment($pet, $faker, $users['groomer'], $groomingServices);
        }

        if (! LabRequisition::whereHas('medicalRecord', function ($query) use ($pet) {
            $query->where('pet_id', $pet->id);
        })->exists()) {
            $this->createLabRequisitions($medicalRecords, $faker, $users['vet'], $labTests);
        }
    }

    private function createAppointments(Pet $pet, $faker, User $vet): Collection
    {
        $appointments = collect();
        $types = ['consultation', 'vaccination', 'follow_up', 'emergency', 'other'];
        $count = $faker->numberBetween(4, 6);

        for ($index = $count; $index >= 1; $index--) {
            $date = Carbon::now()->subWeeks($index * $faker->numberBetween(3, 6))->setTime($faker->numberBetween(8, 16), 0);

            $appointments->push(Appointment::create([
                'pet_id' => $pet->id,
                'veterinarian_id' => $vet->id,
                'appointment_date' => $date,
                'type' => $faker->randomElement($types),
                'status' => 'completed',
                'notes' => $faker->sentence(10),
                'arrival_time' => (clone $date)->subMinutes(15),
                'queue_status' => 'completed',
                'queue_priority' => $faker->numberBetween(0, 2),
                'estimated_wait_time' => $faker->numberBetween(10, 35),
                'reminder_sent' => true,
                'reminder_sent_at' => (clone $date)->subDay(),
                'created_at' => (clone $date)->subDays(2),
                'updated_at' => $date,
            ]));
        }

        return $appointments;
    }

    private function createMedicalRecords(Pet $pet, Collection $appointments, $faker, User $vet): Collection
    {
        $profiles = [
            ['complaint' => 'Annual wellness assessment', 'diagnosis' => 'Healthy and stable', 'treatment' => 'Continue preventive care and balanced diet.'],
            ['complaint' => 'Intermittent vomiting', 'diagnosis' => 'Mild gastroenteritis', 'treatment' => 'Short-term GI diet, hydration support, and monitoring.'],
            ['complaint' => 'Itchy skin and paw licking', 'diagnosis' => 'Allergic dermatitis', 'treatment' => 'Medicated shampoo and antihistamine support.'],
            ['complaint' => 'Reduced activity and limping', 'diagnosis' => 'Soft tissue strain', 'treatment' => 'Rest, pain control, and two-week recheck.'],
            ['complaint' => 'Dental tartar buildup', 'diagnosis' => 'Stage 1 periodontal disease', 'treatment' => 'Dental cleaning and home oral care plan.'],
        ];

        return $appointments
            ->take(4)
            ->values()
            ->map(function (Appointment $appointment, int $index) use ($pet, $faker, $vet, $profiles) {
                $profile = $profiles[$index % count($profiles)];
                $visitDate = Carbon::parse($appointment->appointment_date);

                return MedicalRecord::create([
                    'pet_id' => $pet->id,
                    'veterinarian_id' => $vet->id,
                    'appointment_id' => $appointment->id,
                    'visit_date' => $visitDate->toDateString(),
                    'complaint' => $profile['complaint'],
                    'examination_notes' => $faker->sentence(14),
                    'temperature' => $faker->randomFloat(1, 37.8, 39.5),
                    'heart_rate' => $faker->numberBetween(70, 135),
                    'respiratory_rate' => $faker->numberBetween(18, 32),
                    'blood_pressure' => $faker->numberBetween(105, 135) . '/' . $faker->numberBetween(70, 90),
                    'diagnosis' => $profile['diagnosis'],
                    'treatment_plan' => $profile['treatment'],
                    'follow_up_date' => $visitDate->copy()->addDays($faker->numberBetween(7, 30))->toDateString(),
                    'created_at' => $visitDate,
                    'updated_at' => $visitDate,
                ]);
            });
    }

    private function createPrescriptions(Collection $medicalRecords, $faker, User $staff): void
    {
        $medications = [
            ['name' => 'Amoxicillin', 'dosage' => '250 mg', 'frequency' => 'Twice daily', 'duration_days' => 7, 'quantity' => 14],
            ['name' => 'Carprofen', 'dosage' => '50 mg', 'frequency' => 'Once daily', 'duration_days' => 5, 'quantity' => 5],
            ['name' => 'Apoquel', 'dosage' => '16 mg', 'frequency' => 'Twice daily', 'duration_days' => 14, 'quantity' => 28],
            ['name' => 'Omeprazole', 'dosage' => '10 mg', 'frequency' => 'Once daily', 'duration_days' => 10, 'quantity' => 10],
        ];

        foreach ($medicalRecords->take(3) as $record) {
            $medication = $faker->randomElement($medications);
            $createdAt = Carbon::parse($record->visit_date)->addHours(2);

            Prescription::create([
                'medical_record_id' => $record->id,
                'assigned_staff_id' => $staff->id,
                'medication_name' => $medication['name'],
                'dosage' => $medication['dosage'],
                'frequency' => $medication['frequency'],
                'duration_days' => $medication['duration_days'],
                'quantity' => $medication['quantity'],
                'instructions' => $faker->sentence(12),
                'dispensed' => $faker->boolean(80),
                'dispensed_at' => $createdAt->copy()->addMinutes(30),
                'dispensed_by' => $staff->id,
                'refill_reminder_sent' => $faker->boolean(25),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    private function createVaccinations(Pet $pet, $faker, User $vet): void
    {
        $vaccines = [
            ['name' => 'DHPP', 'type' => 'Core Vaccine'],
            ['name' => 'Rabies', 'type' => 'Core Vaccine'],
            ['name' => 'Bordetella', 'type' => 'Lifestyle Vaccine'],
        ];

        foreach ($vaccines as $index => $vaccine) {
            $date = Carbon::now()->subMonths(10 - ($index * 3));

            PetVaccination::create([
                'pet_id' => $pet->id,
                'vaccine_name' => $vaccine['name'],
                'vaccine_type' => $vaccine['type'],
                'manufacturer' => $faker->company(),
                'batch_number' => strtoupper(Str::random(10)),
                'dose_number' => $index + 1,
                'administered_date' => $date->toDateString(),
                'vaccination_date' => $date,
                'next_due_date' => $date->copy()->addYear()->toDateString(),
                'veterinarian_id' => $vet->id,
                'administered_by' => $vet->id,
                'notes' => $faker->sentence(10),
                'reminder_sent' => $faker->boolean(40),
                'status' => 'administered',
            ]);
        }
    }

    private function createSurgeries(Pet $pet, Collection $medicalRecords, $faker, User $vet, Collection $surgeryTypes): void
    {
        $record = $medicalRecords->random();
        $type = $surgeryTypes->random();
        $scheduledDate = Carbon::parse($record->visit_date)->addWeeks(2)->setTime(9, 30);

        Surgery::create([
            'pet_id' => $pet->id,
            'surgeon_id' => $vet->id,
            'medical_record_id' => $record->id,
            'surgery_type_id' => $type->id,
            'scheduled_date' => $scheduledDate,
            'anesthesia_type' => $faker->randomElement(['General inhalant', 'IV sedation', 'Local with sedation']),
            'pre_op_notes' => $faker->sentence(12),
            'surgery_notes' => $faker->sentence(16),
            'post_op_instructions' => $faker->sentence(14),
            'outcome' => $faker->randomElement(['Recovered well', 'Stable post-op recovery', 'Discharged with medications']),
            'status' => 'completed',
            'created_at' => $scheduledDate,
            'updated_at' => $scheduledDate,
        ]);
    }

    private function createIncidents(Pet $pet, $faker, User $staff, Collection $cages): void
    {
        $incidentDate = Carbon::now()->subWeeks($faker->numberBetween(2, 12))->setTime(14, 0);
        $cage = $cages->isNotEmpty() ? $cages->random() : null;

        Incident::create([
            'incident_number' => 'INC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'incident_date' => $incidentDate,
            'incident_type' => $faker->randomElement(['pet_injury', 'pet_illness', 'pet_aggression', 'other']),
            'severity' => $faker->randomElement(['minor', 'moderate', 'severe']),
            'pet_id' => $pet->id,
            'affected_user_id' => null,
            'location' => $cage ? $cage->location : 'Treatment Room',
            'cage_id' => $cage?->id,
            'description' => $faker->sentence(18),
            'immediate_action_taken' => $faker->sentence(12),
            'root_cause' => $faker->sentence(10),
            'corrective_action' => $faker->sentence(10),
            'status' => $faker->randomElement(['resolved', 'closed']),
            'resolved_date' => $incidentDate->copy()->addHours(6),
            'reported_by' => $staff->id,
            'reported_at' => $incidentDate->copy()->addMinutes(15),
            'created_at' => $incidentDate,
            'updated_at' => $incidentDate,
        ]);
    }

    private function createChronicCondition(Pet $pet, $faker, Collection $medicalRecords): void
    {
        $record = $medicalRecords->random();

        ChronicCondition::create([
            'pet_id' => $pet->id,
            'medical_record_id' => $record->id,
            'condition_name' => $faker->randomElement(['Arthritis', 'Chronic dermatitis', 'Dental disease']),
            'diagnosed_date' => Carbon::parse($record->visit_date)->subWeeks(2)->toDateString(),
            'ongoing_treatment' => $faker->sentence(12),
            'notes' => $faker->sentence(10),
            'is_active' => true,
            'created_at' => Carbon::parse($record->visit_date),
            'updated_at' => Carbon::parse($record->visit_date),
        ]);
    }

    private function createAllergy(Pet $pet, $faker, Collection $medicalRecords): void
    {
        $record = $medicalRecords->random();

        PetAllergy::create([
            'pet_id' => $pet->id,
            'medical_record_id' => $record->id,
            'allergen' => $faker->randomElement(['Chicken protein', 'Dust mites', 'Grass pollen']),
            'reaction_type' => $faker->randomElement(['Skin irritation', 'GI upset', 'Ear inflammation']),
            'severity' => $faker->randomElement(['mild', 'moderate', 'severe']),
            'diagnosed_date' => Carbon::parse($record->visit_date)->toDateString(),
            'notes' => $faker->sentence(10),
            'is_active' => true,
            'created_at' => Carbon::parse($record->visit_date),
            'updated_at' => Carbon::parse($record->visit_date),
        ]);
    }

    private function createCageAssignment(Pet $pet, $faker, Collection $cages): void
    {
        $cage = $cages->first();

        if (! $cage) {
            return;
        }

        $startDate = Carbon::now()->subDays($faker->numberBetween(20, 60));
        $endDate = $startDate->copy()->addDays($faker->numberBetween(2, 5));

        CageAssignment::create([
            'cage_id' => $cage->id,
            'pet_id' => $pet->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'check_in_time' => $startDate->copy()->setTime(8, 0),
            'check_out_time' => $endDate->copy()->setTime(10, 0),
            'feeding_schedule' => 'Twice daily',
            'feeding_times' => '08:00, 18:00',
            'special_diet_notes' => $faker->boolean(40) ? $faker->sentence(6) : null,
            'medication_instructions' => $faker->boolean(35) ? $faker->sentence(7) : null,
            'medication_times' => $faker->boolean(35) ? '09:00, 21:00' : null,
            'notes' => $faker->sentence(10),
            'daily_rate' => $faker->randomFloat(2, 450, 950),
            'checkout_reminder_sent' => true,
            'created_at' => $startDate,
            'updated_at' => $endDate,
        ]);

        $cage->syncStatus();
    }

    private function createGroomingAppointment(Pet $pet, $faker, User $groomer, Collection $groomingServices): void
    {
        $service = $groomingServices->random();
        $appointmentDate = Carbon::now()->subWeeks($faker->numberBetween(1, 8))->setTime(11, 0);

        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'veterinarian_id' => null,
            'appointment_date' => $appointmentDate,
            'type' => 'grooming',
            'status' => 'completed',
            'notes' => $faker->sentence(10),
            'arrival_time' => $appointmentDate->copy()->subMinutes(10),
            'queue_status' => 'completed',
            'queue_priority' => 0,
            'estimated_wait_time' => 10,
            'reminder_sent' => true,
            'reminder_sent_at' => $appointmentDate->copy()->subDay(),
            'created_at' => $appointmentDate->copy()->subDays(2),
            'updated_at' => $appointmentDate,
        ]);

        GroomingAppointment::create([
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'groomer_id' => $groomer->id,
            'special_instructions' => $faker->sentence(8),
            'status' => 'completed',
            'actual_duration_minutes' => $service->duration_minutes,
            'created_at' => $appointmentDate,
            'updated_at' => $appointmentDate,
        ]);
    }

    private function createLabRequisitions(Collection $medicalRecords, $faker, User $vet, Collection $labTests): void
    {
        foreach ($medicalRecords->take(2) as $record) {
            $requestedDate = Carbon::parse($record->visit_date)->addHours(1);
            $completed = $faker->boolean(70);

            LabRequisition::create([
                'medical_record_id' => $record->id,
                'test_id' => $labTests->random()->id,
                'requested_by' => $vet->id,
                'requested_date' => $requestedDate,
                'sample_collected' => true,
                'sample_collection_date' => $requestedDate->copy()->addMinutes(30),
                'status' => $completed ? 'completed' : 'sent_to_lab',
                'results' => $completed ? $faker->sentence(14) : null,
                'result_date' => $completed ? $requestedDate->copy()->addDays(2) : null,
                'notes' => $faker->sentence(8),
                'result_notification_sent' => $completed,
                'created_at' => $requestedDate,
                'updated_at' => $requestedDate,
            ]);
        }
    }
}