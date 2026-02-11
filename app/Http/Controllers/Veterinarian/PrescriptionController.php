<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Pet;
use App\Models\Medication;
use App\Models\User;
use Carbon\Carbon;

class PrescriptionController extends Controller
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

        $medications = Medication::orderBy('name')->get();

        return view('veterinarian.prescriptions.create', compact('pet', 'medications', 'veterinarian'));
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
            'medication_id' => 'required|exists:medications,id',
            'diagnosis' => 'required|string|max:500',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:100',
            'instructions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        Prescription::create([
            'pet_id' => $petId,
            'veterinarian_id' => $veterinarian->id,
            'medication_id' => $request->medication_id,
            'diagnosis' => $request->diagnosis,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'instructions' => $request->instructions,
            'notes' => $request->notes,
            'prescription_date' => now(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('veterinarian.patients.show', $petId)
            ->with('success', 'Prescription created successfully!');
    }

    public function show($petId, $prescriptionId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $prescription = Prescription::where(function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id)
                  ->orWhereNull('veterinarian_id');
        })
            ->where('pet_id', $petId)
            ->with(['pet', 'pet.owner', 'veterinarian', 'medication'])
            ->findOrFail($prescriptionId);

        return view('veterinarian.prescriptions.show', compact('prescription'));
    }

    public function edit($petId, $prescriptionId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $prescription = Prescription::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->with(['pet', 'pet.owner'])
            ->findOrFail($prescriptionId);

        $medications = Medication::orderBy('name')->get();

        return view('veterinarian.prescriptions.edit', compact('prescription', 'medications'));
    }

    public function update(Request $request, $petId, $prescriptionId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $prescription = Prescription::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($prescriptionId);

        $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'diagnosis' => 'required|string|max:500',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:100',
            'instructions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        $prescription->update([
            'medication_id' => $request->medication_id,
            'diagnosis' => $request->diagnosis,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'instructions' => $request->instructions,
            'notes' => $request->notes,
            'updated_at' => now()
        ]);

        return redirect()->route('veterinarian.prescriptions.show', [$petId, $prescriptionId])
            ->with('success', 'Prescription updated successfully!');
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
            // Show prescriptions for specific pet
            $prescriptions = Prescription::where(function($query) use ($veterinarian, $petId) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orWhere(function($subQuery) use ($petId) {
                          $subQuery->where('pet_id', $petId)
                                   ->whereNull('veterinarian_id');
                      });
            })
                ->where('pet_id', $petId)
                ->with(['pet', 'pet.owner', 'medication'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Show all prescriptions for this veterinarian
            $prescriptions = Prescription::where(function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orWhereNull('veterinarian_id');
            })
                ->with(['pet', 'pet.owner', 'medication'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('veterinarian.prescriptions.index', compact('prescriptions', 'petId'));
    }

    public function updateStatus(Request $request, $petId, $prescriptionId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $prescription = Prescription::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($prescriptionId);

        $request->validate([
            'status' => 'required|in:active,completed,discontinued'
        ]);

        $prescription->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Prescription status updated successfully!');
    }
}
