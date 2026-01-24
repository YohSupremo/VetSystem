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
     */
    public function index()
    {
        $medicalRecords = MedicalRecord::with('pet', 'veterinarian')->paginate(15);
        return view('admin.medical-records.index', compact('medicalRecords'));
    }

    /**
     * Show the form for creating a new medical record.
     */
    public function create()
    {
        $pets = Pet::with('owner')->get();
        $veterinarians = User::where('role', 'veterinarian')->where('is_active', 1)->get();
        return view('admin.medical-records.create', compact('pets', 'veterinarians'));
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
        ]);

        $vitalSigns = [
            'temperature' => $request->temperature,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
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
            'vital_signs' => json_encode($vitalSigns),
        ]);

        return redirect()->route('admin.medical-records.index')
            ->with('success', 'Medical record created successfully!');
    }

    /**
     * Display the specified medical record.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $record = $medicalRecord->load('pet', 'veterinarian', 'prescriptions');
        $vitalSigns = $record->vital_signs ? json_decode($record->vital_signs, true) : [];
        return view('admin.medical-records.show', compact('record', 'vitalSigns'));
    }

    /**
     * Show the form for editing the specified medical record.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        $record = $medicalRecord;
        $veterinarians = User::where('role', 'veterinarian')->where('is_active', 1)->get();
        $vitalSigns = $record->vital_signs ? json_decode($record->vital_signs, true) : [];
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
        ]);

        $vitalSigns = [
            'temperature' => $request->temperature,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
        ];

        $medicalRecord->update([
            'veterinarian_id' => $validated['veterinarian_id'],
            'visit_date' => $validated['visit_date'],
            'complaint' => $validated['complaint'],
            'examination_notes' => $validated['examination_notes'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'vital_signs' => json_encode($vitalSigns),
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
}
