<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Pet;
use App\Models\Medication;

class PrescriptionController extends Controller
{
    public function create($petId)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $pet = Pet::with('owner')
            ->whereHas('appointments', function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id);
            })
            ->findOrFail($petId);

        $medications = Medication::orderBy('name')->get();

        return view('veterinarian.prescriptions.create', compact('pet', 'medications'));
    }

    public function store(Request $request, $petId)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $pet = Pet::whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })
        ->findOrFail($petId);

        $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration' => 'required|string',
            'instructions' => 'required|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $prescription = Prescription::create([
            'pet_id' => $pet->id,
            'veterinarian_id' => $veterinarian->id,
            'medication_id' => $request->medication_id,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'instructions' => $request->instructions,
            'diagnosis' => $request->diagnosis,
            'notes' => $request->notes,
            'prescribed_date' => now(),
            'status' => 'active'
        ]);

        return redirect()->route('veterinarian.patients.show', $pet->id)
            ->with('success', 'Prescription created successfully.');
    }

    public function show($petId, $prescriptionId)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $prescription = Prescription::with(['pet', 'pet.owner', 'veterinarian', 'medication'])
            ->where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($prescriptionId);

        return view('veterinarian.prescriptions.show', compact('prescription'));
    }

    public function edit($petId, $prescriptionId)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $prescription = Prescription::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($prescriptionId);

        $medications = Medication::orderBy('name')->get();

        return view('veterinarian.prescriptions.edit', compact('prescription', 'medications'));
    }

    public function update(Request $request, $petId, $prescriptionId)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $prescription = Prescription::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($prescriptionId);

        $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration' => 'required|string',
            'instructions' => 'required|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,completed,discontinued'
        ]);

        $prescription->update($request->all());

        return redirect()->route('veterinarian.prescriptions.show', [$petId, $prescriptionId])
            ->with('success', 'Prescription updated successfully.');
    }
}
