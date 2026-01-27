<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of medical records.
     * Shows only the most recent record for each unique pet
     */
    public function index()
    {
        // Get the latest medical record for each pet
        $medicalRecords = MedicalRecord::with('pet', 'veterinarian')
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('medical_records')
                    ->groupBy('pet_id');
            })
            ->orderBy('visit_date', 'desc')
            ->paginate(15);
            
        return view('admin.medical-records.index', compact('medicalRecords'));
    }

    /**
     * Show the form for creating a new medical record.
     */
    public function create(Request $request)
    {
        $pets = Pet::with('owner')->get();
        $veterinarians = User::where('role', 'veterinarian')->where('is_active', 1)->get();
        $selectedPetId = $request->get('pet_id');
        return view('admin.medical-records.create', compact('pets', 'veterinarians', 'selectedPetId'));
    }

    /**
     * Store a newly created medical record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'complaint' => 'required|string',
            'examination_notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
            'respiratory_rate' => 'nullable|integer',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'other_vitals' => 'nullable|string',
        ]);

        // Only check for existing records if NOT coming from pet history page
        // If pet_id is in the request (pre-selected), allow adding another record
        if (!$request->has('from_pet_history')) {
            $existingRecord = MedicalRecord::where('pet_id', $validated['pet_id'])
                ->latest()
                ->first();

            if ($existingRecord) {
                $pet = Pet::find($validated['pet_id']);
                return redirect()->route('admin.medical-records.index')
                    ->with('warning', 'This pet already has a medical record. Please view the pet\'s history to see all records.')
                    ->with('pet_id', $validated['pet_id'])
                    ->with('pet_name', $pet->name);
            }
        }

        $vitalSigns = [
            'temperature' => $request->temperature,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'blood_pressure' => $request->blood_pressure_systolic && $request->blood_pressure_diastolic 
                ? $request->blood_pressure_systolic . '/' . $request->blood_pressure_diastolic 
                : null,
            'weight' => $request->weight,
            'other_vitals' => $request->other_vitals,
        ];

        MedicalRecord::create([
            'pet_id' => $validated['pet_id'],
            'veterinarian_id' => $validated['veterinarian_id'],
            'visit_date' => $validated['visit_date'],
            'complaint' => $validated['complaint'],
            'examination_notes' => $validated['examination_notes'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'vital_signs' => $vitalSigns,
        ]);

        // Redirect back to pet history if coming from there
        if ($request->has('from_pet_history')) {
            $pet = Pet::find($validated['pet_id']);
            return redirect()->route('admin.medical-records.pet', $pet->id)
                ->with('success', 'Medical record added to ' . $pet->name . '\'s history successfully!');
        }

        return redirect()->route('admin.medical-records.index')
            ->with('success', 'Medical record created successfully!');
    }

    /**
     * Display the specified medical record.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $record = $medicalRecord->load('pet', 'veterinarian', 'prescriptions');
        return view('admin.medical-records.show', compact('record'));
    }

    /**
     * Show the form for editing the specified medical record.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        $record = $medicalRecord;
        $veterinarians = User::where('role', 'veterinarian')->where('is_active', 1)->get();
        $vitalSigns = $record->vital_signs ?? [];
        return view('admin.medical-records.edit', compact('record', 'veterinarians', 'vitalSigns'));
    }

    /**
     * Update the specified medical record in storage.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'veterinarian_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'complaint' => 'required|string',
            'examination_notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
            'respiratory_rate' => 'nullable|integer',
            'blood_pressure_systolic' => 'nullable|integer',
            'blood_pressure_diastolic' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'other_vitals' => 'nullable|string',
        ]);

        $vitalSigns = [
            'temperature' => $request->temperature,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'blood_pressure' => $request->blood_pressure_systolic && $request->blood_pressure_diastolic 
                ? $request->blood_pressure_systolic . '/' . $request->blood_pressure_diastolic 
                : null,
            'weight' => $request->weight,
            'other_vitals' => $request->other_vitals,
        ];

        $medicalRecord->update([
            'veterinarian_id' => $validated['veterinarian_id'],
            'visit_date' => $validated['visit_date'],
            'complaint' => $validated['complaint'],
            'examination_notes' => $validated['examination_notes'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'vital_signs' => $vitalSigns,
        ]);

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
}
