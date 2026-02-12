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
        // Debug: Log entry into store method
        \Log::info('Prescription store method called');
        \Log::info('Request data: ' . json_encode($request->all()));
        \Log::info('Pet ID: ' . $petId);

        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $pet = Pet::whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })->findOrFail($petId);

        // Debug: Log pet found
        \Log::info('Pet found: ' . $pet->name);

        try {
            $validated = $request->validate([
                'medication_id' => 'nullable|exists:medications,id',  // Temporarily nullable for testing
                'diagnosis' => 'required|string|max:500',
                'dosage' => 'required|string|max:100',
                'frequency' => 'required|string|max:100',
                'duration' => 'required|string|max:100',
                'instructions' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:1000'
            ]);
            
            \Log::info('Validation passed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        // Debug: Log received data
        \Log::info('Prescription data received: ' . json_encode($request->all()));
        \Log::info('Validated data: ' . json_encode($validated));
        \Log::info('Medication ID specifically: ' . $validated['medication_id']);

        try {
            // Handle medication_id explicitly
            $medicationId = $validated['medication_id'] ?? null;
            
            if (!$medicationId) {
                \Log::warning('No medication ID provided - using NULL');
                return redirect()->back()
                    ->with('error', 'Please select a medication')
                    ->withInput();
            }

            $prescription = Prescription::create([
                'pet_id' => $petId,
                'veterinarian_id' => $veterinarian->id,
                'medication_id' => $medicationId,
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

            \Log::info('Prescription created successfully with ID: ' . $prescription->id);
            \Log::info('Prescription medication_id: ' . $prescription->medication_id);

            return redirect()->route('veterinarian.patients.show', $petId)
                ->with('success', 'Prescription created successfully!');

        } catch (\Exception $e) {
            \Log::error('Prescription creation failed: ' . $e->getMessage());
            \Log::error('Prescription creation error trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to create prescription: ' . $e->getMessage())
                ->withInput();
        }
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
