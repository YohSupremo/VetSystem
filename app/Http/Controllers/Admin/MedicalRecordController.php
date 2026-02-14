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
     * Display a listing of medical records - one latest record per pet.
     */
    public function index()
    {
        // Get IDs of the latest record for each pet
        $latestIds = MedicalRecord::selectRaw('MAX(id) as id')
            ->groupBy('pet_id')
            ->pluck('id');

        // Fetch the full records for these IDs
        $records = MedicalRecord::whereIn('id', $latestIds)
            ->with(['pet.owner.user', 'veterinarian'])
            ->orderBy('visit_date', 'desc')
            ->paginate(15);
        
        return view('admin.medical-records.index', compact('records'));
    }

    public function create(Request $request)
    {
        $pets = Pet::with('owner')->get();
        $veterinarians = User::where('role', 'veterinarian')->where('is_active', 1)->get();
        $appointments = \App\Models\Appointment::where('status', '!=', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->get();
        $selectedPetId = $request->get('pet_id');
        
        return view('admin.medical-records.create', compact('pets', 'veterinarians', 'appointments', 'selectedPetId'));
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
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:visit_date',
            'weight' => 'nullable|numeric|min:0',
            'other_vitals' => 'nullable|string',
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

        MedicalRecord::create([
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

        return redirect()->route('admin.medical-records.index')
            ->with('success', 'Medical record created successfully!');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $record = $medicalRecord->load(['pet', 'veterinarian', 'appointment', 'prescriptions']);
        return view('admin.medical-records.show', compact('record'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $record = $medicalRecord;
        $veterinarians = User::where('role', 'veterinarian')->where('is_active', 1)->get();
        $appointments = \App\Models\Appointment::orderBy('appointment_date', 'desc')->get();
        
        // Parse BP if exists
        $bpSystolic = '';
        $bpDiastolic = '';
        if ($record->blood_pressure && strpos($record->blood_pressure, '/') !== false) {
            [$bpSystolic, $bpDiastolic] = explode('/', $record->blood_pressure);
        }

        return view('admin.medical-records.edit', compact('record', 'veterinarians', 'appointments', 'bpSystolic', 'bpDiastolic'));
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
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:visit_date',
            'weight' => 'nullable|numeric|min:0',
            'other_vitals' => 'nullable|string',
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
