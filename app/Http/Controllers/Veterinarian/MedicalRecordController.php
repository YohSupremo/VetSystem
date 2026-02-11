<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;

class MedicalRecordController extends Controller
{
    public function create($petId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $pet = Pet::with('owner')
            ->whereHas('appointments', function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id);
            })
            ->findOrFail($petId);

        return view('veterinarian.medical-records.create', compact('pet', 'veterinarian'));
    }

    public function store(Request $request, $petId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $pet = Pet::whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })->findOrFail($petId);

        $request->validate([
            'chief_complaint' => 'required|string|max:500',
            'symptoms' => 'required|string|max:2000',
            'physical_exam' => 'required|string|max:2000',
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'required|string|max:2000',
            'lab_results' => 'nullable|string|max:2000',
            'follow_up_instructions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        MedicalRecord::create([
            'pet_id' => $petId,
            'veterinarian_id' => $veterinarian->id,
            'chief_complaint' => $request->chief_complaint,
            'symptoms' => $request->symptoms,
            'physical_exam' => $request->physical_exam,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'lab_results' => $request->lab_results,
            'follow_up_instructions' => $request->follow_up_instructions,
            'notes' => $request->notes,
            'record_date' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('veterinarian.patients.show', $petId)
            ->with('success', 'Medical record created successfully!');
    }

    public function show($petId, $recordId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $medicalRecord = MedicalRecord::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->with(['pet', 'pet.owner', 'veterinarian'])
            ->findOrFail($recordId);

        return view('veterinarian.medical-records.show', compact('medicalRecord'));
    }

    public function edit($petId, $recordId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $medicalRecord = MedicalRecord::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->with(['pet', 'pet.owner'])
            ->findOrFail($recordId);

        return view('veterinarian.medical-records.edit', compact('medicalRecord'));
    }

    public function update(Request $request, $petId, $recordId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $medicalRecord = MedicalRecord::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($recordId);

        $request->validate([
            'chief_complaint' => 'required|string|max:500',
            'symptoms' => 'required|string|max:2000',
            'physical_exam' => 'required|string|max:2000',
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'required|string|max:2000',
            'lab_results' => 'nullable|string|max:2000',
            'follow_up_instructions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        $medicalRecord->update([
            'chief_complaint' => $request->chief_complaint,
            'symptoms' => $request->symptoms,
            'physical_exam' => $request->physical_exam,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'lab_results' => $request->lab_results,
            'follow_up_instructions' => $request->follow_up_instructions,
            'notes' => $request->notes,
            'updated_at' => now()
        ]);

        return redirect()->route('veterinarian.medical-records.show', [$petId, $recordId])
            ->with('success', 'Medical record updated successfully!');
    }

    public function index($petId = null)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        if ($petId) {
            // Show medical records for specific pet
            $medicalRecords = MedicalRecord::where(function($query) use ($veterinarian, $petId) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orWhere(function($subQuery) use ($petId) {
                          $subQuery->where('pet_id', $petId)
                                   ->whereNull('veterinarian_id');
                      });
            })
                ->where('pet_id', $petId)
                ->with(['pet', 'pet.owner'])
                ->orderBy('record_date', 'desc')
                ->paginate(10);
        } else {
            // Show all medical records for this veterinarian
            $medicalRecords = MedicalRecord::where(function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orWhereNull('veterinarian_id');
            })
            ->with(['pet', 'pet.owner'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        }

        return view('veterinarian.medical-records.index', compact('medicalRecords', 'petId'));
    }
}
