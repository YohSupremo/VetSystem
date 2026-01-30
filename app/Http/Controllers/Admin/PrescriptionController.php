<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pet;
use App\Models\Prescription;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class PrescriptionController extends BaseController
{
    /**
     * Display a listing of prescriptions.
     */
    public function index()
    {
        $pets = Pet::with('prescriptions', 'owner.user')->paginate(10);
        return view('admin.prescriptions.index', compact('pets'));
    }

    /**
     * Show the form for creating a new prescription.
     * Medical records dropdown shows only records for the selected pet (when pet_id in request).
     */
    public function create()
    {
        $pets = Pet::with('owner')->get();
        $petId = request('pet_id');
        $medicalRecords = $petId
            ? MedicalRecord::where('pet_id', $petId)->with('pet')->orderBy('visit_date', 'desc')->get()
            : collect();
        return view('admin.prescriptions.create', compact('pets', 'medicalRecords'));
    }

    /**
     * Store a newly created prescription in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'medical_record_id' => 'nullable|exists:medical_records,id',
            'medication' => 'required|string|max:150',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        Prescription::create($validated);

        return redirect()->route('admin.prescriptions.index')
            ->with('success', 'Prescription created successfully!');
    }

    /**
     * Display the specified prescription.
     */
    public function show(Prescription $prescription)
    {
        $prescription->load('pet.owner.user', 'medicalRecord.veterinarian');
        return view('admin.prescriptions.show', compact('prescription'));
    }

    /**
     * Show the form for editing the specified prescription.
     * Medical records dropdown shows only records for this prescription's pet.
     */
    public function edit(Prescription $prescription)
    {
        $prescription->load('pet.owner.user');
        $pets = Pet::with('owner.user')->get();
        $medicalRecords = MedicalRecord::where('pet_id', $prescription->pet_id)
            ->with('pet', 'veterinarian')
            ->orderBy('visit_date', 'desc')
            ->get();
        return view('admin.prescriptions.edit', compact('prescription', 'pets', 'medicalRecords'));
    }

    /**
     * Update the specified prescription in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'medical_record_id' => 'nullable|exists:medical_records,id',
            'medication' => 'required|string|max:150',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration_days' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        $prescription->update($validated);

        return redirect()->route('admin.prescriptions.show', $prescription->id)
            ->with('success', 'Prescription updated successfully!');
    }

    /**
     * Remove the specified prescription from storage.
     */
    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('admin.prescriptions.index')
            ->with('success', 'Prescription deleted successfully!');
    }

    /**
     * Display prescriptions for a specific pet.
     */
    public function byPet($petId)
    {
        $pet = Pet::with('owner.user')->findOrFail($petId);
        $prescriptions = Prescription::where('pet_id', $petId)
            ->with('medicalRecord')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.prescriptions.pet', compact('pet', 'prescriptions'));
    }
}
