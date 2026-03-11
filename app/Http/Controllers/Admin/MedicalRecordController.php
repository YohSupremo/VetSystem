<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChronicCondition;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\PetAllergy;
use App\Models\User;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of medical records - one latest record per pet.
     */
    public function index(Request $request)
    {
        $showTrash = $request->boolean('trash');
        $search = $request->input('search');

        if ($showTrash) {
            // In trash view, show ALL trashed records individually
            $query = MedicalRecord::onlyTrashed()
                ->with(['pet.owner.user', 'veterinarian']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('pet', function ($petQ) use ($search) {
                        $petQ->where('name', 'like', "%{$search}%")
                             ->orWhere('species', 'like', "%{$search}%")
                             ->orWhere('breed', 'like', "%{$search}%");
                    })
                    ->orWhereHas('pet.owner.user', function ($ownerQ) use ($search) {
                        $ownerQ->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('veterinarian', function ($vetQ) use ($search) {
                        $vetQ->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhere('complaint', 'like', "%{$search}%")
                    ->orWhere('diagnosis', 'like', "%{$search}%");
                });
            }

            $records = $query->orderBy('deleted_at', 'desc')
                ->paginate(15)
                ->appends($request->query());
        } else {
            // Build base query with optional search
            $baseQuery = MedicalRecord::query();

            if ($search) {
                $baseQuery->where(function ($q) use ($search) {
                    $q->whereHas('pet', function ($petQ) use ($search) {
                        $petQ->where('name', 'like', "%{$search}%")
                             ->orWhere('species', 'like', "%{$search}%")
                             ->orWhere('breed', 'like', "%{$search}%");
                    })
                    ->orWhereHas('pet.owner.user', function ($ownerQ) use ($search) {
                        $ownerQ->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('veterinarian', function ($vetQ) use ($search) {
                        $vetQ->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhere('complaint', 'like', "%{$search}%")
                    ->orWhere('diagnosis', 'like', "%{$search}%");
                });
            }

            // Get IDs of the latest record for each pet (within filtered set)
            $latestIds = (clone $baseQuery)->selectRaw('MAX(id) as id')
                ->groupBy('pet_id')
                ->pluck('id');

            $records = MedicalRecord::whereIn('id', $latestIds)
                ->with(['pet.owner.user', 'veterinarian'])
                ->orderBy('visit_date', 'desc')
                ->paginate(15)
                ->appends($request->query());
        }
        
        return view('admin.medical-records.index', compact('records', 'showTrash'));
    }

    public function restore(int $id)
    {
        $record = MedicalRecord::onlyTrashed()->findOrFail($id);
        $record->restore();

        return redirect()->route('admin.medical-records.index', ['trash' => 1])
            ->with('success', 'Medical record restored successfully.');
    }

    public function create(Request $request)
    {
        $pets = Pet::with([
            'owner.user',
            'petAllergies' => function ($query) {
                $query->where('is_active', 1)
                    ->whereNotNull('allergen')
                    ->where('allergen', '!=', '')
                    ->whereNotNull('reaction_type')
                    ->where('reaction_type', '!=', '')
                    ->whereNotNull('severity')
                    ->where('severity', '!=', '')
                    ->orderByDesc('severity')
                    ->orderBy('allergen');
            },
        ])->get();
        $veterinarians = User::where('role', 'veterinarian')->where('is_active', 1)->get();
        
        // Filter appointments for veterinarians
        $appointmentsQuery = \App\Models\Appointment::where('status', '!=', 'completed');
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            $appointmentsQuery->where('veterinarian_id', $user->id);
        }
        $appointments = $appointmentsQuery->orderBy('appointment_date', 'desc')->get();
        
        $selectedPetId = $request->get('pet_id');
        $allergyMap = $pets->mapWithKeys(function ($pet) {
            return [
                (string) $pet->id => $pet->petAllergies->map(function ($allergy) {
                    return [
                        'allergen' => $allergy->allergen,
                        'reaction_type' => $allergy->reaction_type,
                        'severity' => $allergy->severity,
                    ];
                })->values()->all(),
            ];
        })->all();
        
        return view('admin.medical-records.create', compact('pets', 'veterinarians', 'appointments', 'selectedPetId', 'allergyMap'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'visit_date' => 'required|date',
            'complaint' => 'required|string',
            'examination_notes' => 'nullable|string',
            'temperature' => 'nullable|numeric|between:30,45',
            'heart_rate' => 'nullable|integer|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'diagnosis' => 'required_if:mark_as_chronic,1|nullable|string',
            'treatment_plan' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:visit_date',
            'weight' => 'nullable|numeric|min:0',
            'other_vitals' => 'nullable|string',
            'mark_as_chronic' => 'nullable|boolean',
            'mark_as_allergy' => 'nullable|boolean',
            'chronic_condition_name' => 'required_if:mark_as_chronic,1|nullable|string|max:150',
            'chronic_ongoing_treatment' => 'nullable|string',
            'allergy_allergen' => 'required_if:mark_as_allergy,1|nullable|string|max:150',
            'allergy_reaction_type' => 'nullable|string|max:100',
            'allergy_severity' => 'required_if:mark_as_allergy,1|nullable|in:mild,moderate,severe',
        ], [
            'diagnosis.required_if' => 'Diagnosis is required when marking as chronic condition.',
            'chronic_condition_name.required_if' => 'Condition name is required when marking as chronic condition.',
            'allergy_allergen.required_if' => 'Allergen is required when marking as allergy.',
            'allergy_severity.required_if' => 'Severity is required when marking as allergy.',
        ]);

        // Combine BP
        $bloodPressure = null;
        if ($request->blood_pressure_systolic && $request->blood_pressure_diastolic) {
            $bloodPressure = $request->blood_pressure_systolic . '/' . $request->blood_pressure_diastolic;
        }

        // Update Pet Weight if provided
        if ($request->filled('weight')) {
            $pet = Pet::find($validated['pet_id']);
            if ($pet) {
                $pet->update(['weight' => $request->weight]);
            }
        }

        // Append other_vitals to examination_notes if present
        $examNotes = $validated['examination_notes'] ?? '';
        if ($request->filled('other_vitals')) {
            $examNotes .= "\n\nOther Vitals: " . $request->other_vitals;
        }

        $medicalRecord = MedicalRecord::create([
            'pet_id' => $validated['pet_id'],
            'veterinarian_id' => $validated['veterinarian_id'],
            'appointment_id' => $validated['appointment_id'],
            'visit_date' => $validated['visit_date'],
            'complaint' => $validated['complaint'],
            'examination_notes' => $examNotes,
            'temperature' => $validated['temperature'],
            'heart_rate' => $validated['heart_rate'],
            'respiratory_rate' => $validated['respiratory_rate'],
            'blood_pressure' => $bloodPressure,
            'diagnosis' => $validated['diagnosis'],
            'treatment_plan' => $validated['treatment_plan'],
            'follow_up_date' => $validated['follow_up_date'],
        ]);

        if ($request->boolean('mark_as_chronic')) {
            $this->upsertChronicConditionFromRecord($medicalRecord, [
                'condition_name' => trim((string) ($validated['chronic_condition_name'] ?? '')),
                'ongoing_treatment' => isset($validated['chronic_ongoing_treatment']) ? trim((string) $validated['chronic_ongoing_treatment']) : null,
            ]);
        }

        if ($request->boolean('mark_as_allergy')) {
            $this->upsertPetAllergyFromRecord($medicalRecord, [
                'allergen' => trim((string) ($validated['allergy_allergen'] ?? '')),
                'reaction_type' => isset($validated['allergy_reaction_type']) ? trim((string) $validated['allergy_reaction_type']) : null,
                'severity' => $validated['allergy_severity'] ?? null,
            ]);
        }

        return redirect()->route('admin.medical-records.index')
            ->with('success', 'Medical record created successfully!');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $record = $medicalRecord->load(['pet', 'veterinarian', 'appointment', 'prescriptions', 'pet.chronicConditions']);

        $linkedChronicConditions = ChronicCondition::query()
            ->where('pet_id', $record->pet_id)
            ->where('medical_record_id', $record->id)
            ->orderByDesc('id')
            ->get();

        $linkedPetAllergies = PetAllergy::query()
            ->where('pet_id', $record->pet_id)
            ->where('medical_record_id', $record->id)
            ->orderByDesc('id')
            ->get();

        $diagnosis = trim((string) ($record->diagnosis ?? ''));
        $alreadyMarkedAsChronic = false;

        if ($diagnosis !== '') {
            $alreadyMarkedAsChronic = ChronicCondition::query()
                ->where('pet_id', $record->pet_id)
                ->where('is_active', 1)
                ->whereRaw('LOWER(condition_name) = ?', [mb_strtolower($diagnosis)])
                ->exists();
        }

        return view('admin.medical-records.show', compact('record', 'alreadyMarkedAsChronic', 'linkedChronicConditions', 'linkedPetAllergies'));
    }

    public function markAsChronic(MedicalRecord $medicalRecord)
    {
        $diagnosis = trim((string) ($medicalRecord->diagnosis ?? ''));

        if ($diagnosis === '') {
            return back()->withErrors([
                'error' => 'Cannot mark as chronic because diagnosis is empty.',
            ]);
        }

        $conditionName = mb_substr($diagnosis, 0, 150);

        $exists = ChronicCondition::query()
            ->where('pet_id', $medicalRecord->pet_id)
            ->where('is_active', 1)
            ->whereRaw('LOWER(condition_name) = ?', [mb_strtolower($conditionName)])
            ->exists();

        if ($exists) {
            return back()->with('success', 'This diagnosis is already marked as a chronic condition for the pet.');
        }

        ChronicCondition::create([
            'pet_id' => $medicalRecord->pet_id,
            'medical_record_id' => $medicalRecord->id,
            'condition_name' => $conditionName,
            'diagnosed_date' => $medicalRecord->visit_date,
            'ongoing_treatment' => $medicalRecord->treatment_plan,
            'notes' => null,
            'is_active' => 1,
        ]);

        return back()->with('success', 'Diagnosis marked as chronic condition successfully.');
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $record = $medicalRecord->load([
            'pet.petAllergies' => function ($query) {
                $query->where('is_active', 1)
                    ->whereNotNull('allergen')
                    ->where('allergen', '!=', '')
                    ->whereNotNull('reaction_type')
                    ->where('reaction_type', '!=', '')
                    ->whereNotNull('severity')
                    ->where('severity', '!=', '')
                    ->orderByDesc('severity')
                    ->orderBy('allergen');
            },
        ]);
        $veterinarians = User::where('role', 'veterinarian')->where('is_active', 1)->get();
        
        // Filter appointments for veterinarians
        $appointmentsQuery = \App\Models\Appointment::query();
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            $appointmentsQuery->where('veterinarian_id', $user->id);
        }
        $appointments = $appointmentsQuery->orderBy('appointment_date', 'desc')->get();
        
        // Parse BP if exists
        $bpSystolic = '';
        $bpDiastolic = '';
        if ($record->blood_pressure && strpos($record->blood_pressure, '/') !== false) {
            [$bpSystolic, $bpDiastolic] = explode('/', $record->blood_pressure);
        }

        $diagnosis = trim((string) ($record->diagnosis ?? ''));
        $existingChronicForDiagnosis = false;
        if ($diagnosis !== '') {
            $existingChronicForDiagnosis = ChronicCondition::query()
                ->where('pet_id', $record->pet_id)
                ->where('is_active', 1)
                ->whereRaw('LOWER(condition_name) = ?', [mb_strtolower($diagnosis)])
                ->exists();
        }

        $activeAllergies = optional($record->pet)->petAllergies ?? collect();
        $linkedChronic = ChronicCondition::query()
            ->where('pet_id', $record->pet_id)
            ->where('medical_record_id', $record->id)
            ->first();
        $linkedAllergy = PetAllergy::query()
            ->where('pet_id', $record->pet_id)
            ->where('medical_record_id', $record->id)
            ->first();

        return view('admin.medical-records.edit', compact('record', 'veterinarians', 'appointments', 'bpSystolic', 'bpDiastolic', 'existingChronicForDiagnosis', 'activeAllergies', 'linkedChronic', 'linkedAllergy'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'veterinarian_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'visit_date' => 'required|date',
            'complaint' => 'required|string',
            'examination_notes' => 'nullable|string',
            'temperature' => 'nullable|numeric|between:30,45',
            'heart_rate' => 'nullable|integer|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'diagnosis' => 'required_if:mark_as_chronic,1|nullable|string',
            'treatment_plan' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:visit_date',
            'weight' => 'nullable|numeric|min:0',
            'other_vitals' => 'nullable|string',
            'mark_as_chronic' => 'nullable|boolean',
            'mark_as_allergy' => 'nullable|boolean',
            'chronic_condition_name' => 'required_if:mark_as_chronic,1|nullable|string|max:150',
            'chronic_ongoing_treatment' => 'nullable|string',
            'allergy_allergen' => 'required_if:mark_as_allergy,1|nullable|string|max:150',
            'allergy_reaction_type' => 'nullable|string|max:100',
            'allergy_severity' => 'required_if:mark_as_allergy,1|nullable|in:mild,moderate,severe',
        ], [
            'diagnosis.required_if' => 'Diagnosis is required when marking as chronic condition.',
            'chronic_condition_name.required_if' => 'Condition name is required when marking as chronic condition.',
            'allergy_allergen.required_if' => 'Allergen is required when marking as allergy.',
            'allergy_severity.required_if' => 'Severity is required when marking as allergy.',
        ]);

        // Combine BP
        $bloodPressure = null;
        if ($request->blood_pressure_systolic && $request->blood_pressure_diastolic) {
            $bloodPressure = $request->blood_pressure_systolic . '/' . $request->blood_pressure_diastolic;
        }

        // Update Pet Weight if provided
        if ($request->filled('weight')) {
            $medicalRecord->pet->update(['weight' => $request->weight]);
        }

        // Append other_vitals to examination_notes if present
        $examNotes = $validated['examination_notes'] ?? '';
        if ($request->filled('other_vitals')) {
            $examNotes .= "\n\nOther Vitals: " . $request->other_vitals;
        }

        $medicalRecord->update([
            'veterinarian_id' => $validated['veterinarian_id'],
            'appointment_id' => $validated['appointment_id'],
            'visit_date' => $validated['visit_date'],
            'complaint' => $validated['complaint'],
            'examination_notes' => $examNotes,
            'temperature' => $validated['temperature'],
            'heart_rate' => $validated['heart_rate'],
            'respiratory_rate' => $validated['respiratory_rate'],
            'blood_pressure' => $bloodPressure,
            'diagnosis' => $validated['diagnosis'],
            'treatment_plan' => $validated['treatment_plan'],
            'follow_up_date' => $validated['follow_up_date'],
        ]);

        if ($request->boolean('mark_as_chronic')) {
            $this->upsertChronicConditionFromRecord($medicalRecord->fresh(), [
                'condition_name' => trim((string) ($validated['chronic_condition_name'] ?? '')),
                'ongoing_treatment' => isset($validated['chronic_ongoing_treatment']) ? trim((string) $validated['chronic_ongoing_treatment']) : null,
            ]);
        }

        if ($request->boolean('mark_as_allergy')) {
            $this->upsertPetAllergyFromRecord($medicalRecord->fresh(), [
                'allergen' => trim((string) ($validated['allergy_allergen'] ?? '')),
                'reaction_type' => isset($validated['allergy_reaction_type']) ? trim((string) $validated['allergy_reaction_type']) : null,
                'severity' => $validated['allergy_severity'] ?? null,
            ]);
        }

        return redirect()->route('admin.medical-records.show', $medicalRecord->id)
            ->with('success', 'Medical record updated successfully!');
    }

    /**
     * Remove the specified medical record from storage.
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();
        return redirect()->route('admin.medical-records.index')
            ->with('success', 'Medical record deleted successfully!');
    }

    /**
     * Display complete medical history for a specific pet.
     */
    public function byPet(Pet $pet)
    {
        $medicalRecords = MedicalRecord::where('pet_id', $pet->id)
            ->with('veterinarian')
            ->orderBy('visit_date', 'desc')
            ->get();
        
        return view('admin.medical-records.pet-history', compact('pet', 'medicalRecords'));
    }

    private function upsertChronicConditionFromRecord(MedicalRecord $medicalRecord, array $chronicData = []): void
    {
        $conditionName = trim((string) ($chronicData['condition_name'] ?? ($medicalRecord->diagnosis ?? '')));
        if ($conditionName === '') {
            return;
        }
        $conditionName = mb_substr($conditionName, 0, 150);
        $ongoingTreatment = isset($chronicData['ongoing_treatment']) && trim((string) $chronicData['ongoing_treatment']) !== ''
            ? trim((string) $chronicData['ongoing_treatment'])
            : $medicalRecord->treatment_plan;

        $existingByRecord = ChronicCondition::query()
            ->where('pet_id', $medicalRecord->pet_id)
            ->where('medical_record_id', $medicalRecord->id)
            ->first();

        if ($existingByRecord) {
            $existingByRecord->update([
                'condition_name' => $conditionName,
                'diagnosed_date' => $medicalRecord->visit_date,
                'ongoing_treatment' => $ongoingTreatment,
                'is_active' => 1,
            ]);

            return;
        }

        $existing = ChronicCondition::query()
            ->where('pet_id', $medicalRecord->pet_id)
            ->where('is_active', 1)
            ->whereRaw('LOWER(condition_name) = ?', [mb_strtolower($conditionName)])
            ->first();

        if ($existing) {
            $existing->update([
                'medical_record_id' => $existing->medical_record_id ?: $medicalRecord->id,
                'diagnosed_date' => $medicalRecord->visit_date,
                'ongoing_treatment' => $ongoingTreatment,
                'is_active' => 1,
            ]);

            return;
        }

        ChronicCondition::create([
            'pet_id' => $medicalRecord->pet_id,
            'medical_record_id' => $medicalRecord->id,
            'condition_name' => $conditionName,
            'diagnosed_date' => $medicalRecord->visit_date,
            'ongoing_treatment' => $ongoingTreatment,
            'notes' => null,
            'is_active' => 1,
        ]);
    }

    private function upsertPetAllergyFromRecord(MedicalRecord $medicalRecord, array $allergyData): void
    {
        $existingByRecord = PetAllergy::query()
            ->where('pet_id', $medicalRecord->pet_id)
            ->where('medical_record_id', $medicalRecord->id)
            ->first();

        if ($existingByRecord) {
            $existingByRecord->update([
                'allergen' => $allergyData['allergen'] ?? $existingByRecord->allergen,
                'reaction_type' => $allergyData['reaction_type'] ?? null,
                'severity' => $allergyData['severity'] ?? $existingByRecord->severity,
                'diagnosed_date' => $medicalRecord->visit_date,
                'is_active' => 1,
            ]);

            return;
        }

        $existing = PetAllergy::query()
            ->where('pet_id', $medicalRecord->pet_id)
            ->where('is_active', 1)
            ->whereRaw('LOWER(allergen) = ?', [mb_strtolower((string) ($allergyData['allergen'] ?? ''))])
            ->first();

        if ($existing) {
            $existing->update([
                'medical_record_id' => $existing->medical_record_id ?: $medicalRecord->id,
                'allergen' => $allergyData['allergen'] ?? $existing->allergen,
                'reaction_type' => $allergyData['reaction_type'] ?? null,
                'severity' => $allergyData['severity'] ?? $existing->severity,
                'diagnosed_date' => $medicalRecord->visit_date,
                'is_active' => 1,
            ]);

            return;
        }

        PetAllergy::create([
            'pet_id' => $medicalRecord->pet_id,
            'medical_record_id' => $medicalRecord->id,
            'allergen' => $allergyData['allergen'] ?? '',
            'reaction_type' => $allergyData['reaction_type'] ?? null,
            'severity' => $allergyData['severity'] ?? 'moderate',
            'diagnosed_date' => $medicalRecord->visit_date,
            'notes' => null,
            'is_active' => 1,
        ]);
    }
}
