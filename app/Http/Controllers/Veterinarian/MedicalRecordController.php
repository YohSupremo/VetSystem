<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;

class MedicalRecordController extends Controller
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

        return view('veterinarian.medical-records.create', compact('pet'));
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
            'chief_complaint' => 'required|string',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'symptoms' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'laboratory_results' => 'nullable|string',
            'follow_up_instructions' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $medicalRecord = MedicalRecord::create([
            'pet_id' => $pet->id,
            'veterinarian_id' => $veterinarian->id,
            'chief_complaint' => $request->chief_complaint,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'symptoms' => $request->symptoms,
            'physical_examination' => $request->physical_examination,
            'laboratory_results' => $request->laboratory_results,
            'follow_up_instructions' => $request->follow_up_instructions,
            'notes' => $request->notes,
            'record_date' => now()
        ]);

        return redirect()->route('veterinarian.patients.show', $pet->id)
            ->with('success', 'Medical record created successfully.');
    }

    public function show($petId, $recordId)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $medicalRecord = MedicalRecord::with(['pet', 'pet.owner', 'veterinarian'])
            ->where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($recordId);

        return view('veterinarian.medical-records.show', compact('medicalRecord'));
    }

    public function edit($petId, $recordId)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $medicalRecord = MedicalRecord::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($recordId);

        return view('veterinarian.medical-records.edit', compact('medicalRecord'));
    }

    public function update(Request $request, $petId, $recordId)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $medicalRecord = MedicalRecord::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($recordId);

        $request->validate([
            'chief_complaint' => 'required|string',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'symptoms' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'laboratory_results' => 'nullable|string',
            'follow_up_instructions' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $medicalRecord->update($request->all());

        return redirect()->route('veterinarian.medical-records.show', [$petId, $recordId])
            ->with('success', 'Medical record updated successfully.');
    }
}
