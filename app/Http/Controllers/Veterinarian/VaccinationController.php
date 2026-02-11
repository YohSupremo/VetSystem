<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vaccination;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;

class VaccinationController extends Controller
{
    public function index($petId = null)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        if ($petId) {
            // Show vaccinations for specific pet
            $vaccinations = Vaccination::where(function($query) use ($veterinarian, $petId) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orWhere(function($subQuery) use ($petId) {
                          $subQuery->where('pet_id', $petId)
                                   ->whereNull('veterinarian_id');
                      });
            })
                ->where('pet_id', $petId)
                ->with(['pet', 'pet.owner'])
                ->orderBy('vaccination_date', 'desc')
                ->paginate(10);
        } else {
            // Show all vaccinations for this veterinarian
            $vaccinations = Vaccination::where(function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orWhereNull('veterinarian_id');
            })
                ->with(['pet', 'pet.owner'])
                ->orderBy('vaccination_date', 'desc')
                ->paginate(10);
        }

        return view('veterinarian.vaccinations.index', compact('vaccinations', 'petId'));
    }

    public function create($petId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $pet = Pet::whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id)
                  ->orWhereNull('veterinarian_id');
        })
        ->with('owner.user')
        ->findOrFail($petId);

        return view('veterinarian.vaccinations.create', compact('pet', 'veterinarian'));
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
            $query->where('veterinarian_id', $veterinarian->id)
                  ->orWhereNull('veterinarian_id');
        })->findOrFail($petId);

        $request->validate([
            'vaccine_name' => 'required|string|max:200',
            'vaccine_type' => 'required|string|max:100',
            'manufacturer' => 'nullable|string|max:200',
            'batch_number' => 'nullable|string|max:100',
            'vaccination_date' => 'required|date',
            'next_due_date' => 'nullable|date|after:vaccination_date',
            'notes' => 'nullable|string|max:1000'
        ]);

        Vaccination::create([
            'pet_id' => $petId,
            'veterinarian_id' => $veterinarian->id,
            'vaccine_name' => $request->vaccine_name,
            'vaccine_type' => $request->vaccine_type,
            'manufacturer' => $request->manufacturer,
            'batch_number' => $request->batch_number,
            'vaccination_date' => $request->vaccination_date,
            'next_due_date' => $request->next_due_date,
            'notes' => $request->notes,
            'status' => 'administered',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('veterinarian.patients.show', $petId)
            ->with('success', 'Vaccination recorded successfully!');
    }

    public function show($petId, $vaccinationId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $vaccination = Vaccination::where(function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id)
                  ->orWhereNull('veterinarian_id');
        })
            ->where('pet_id', $petId)
            ->with(['pet', 'pet.owner', 'veterinarian'])
            ->findOrFail($vaccinationId);

        return view('veterinarian.vaccinations.show', compact('vaccination'));
    }

    public function edit($petId, $vaccinationId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $vaccination = Vaccination::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->with(['pet', 'pet.owner'])
            ->findOrFail($vaccinationId);

        return view('veterinarian.vaccinations.edit', compact('vaccination'));
    }

    public function update(Request $request, $petId, $vaccinationId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $vaccination = Vaccination::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($vaccinationId);

        $request->validate([
            'vaccine_name' => 'required|string|max:200',
            'vaccine_type' => 'required|string|max:100',
            'manufacturer' => 'nullable|string|max:200',
            'batch_number' => 'nullable|string|max:100',
            'vaccination_date' => 'required|date',
            'next_due_date' => 'nullable|date|after:vaccination_date',
            'notes' => 'nullable|string|max:1000'
        ]);

        $vaccination->update([
            'vaccine_name' => $request->vaccine_name,
            'vaccine_type' => $request->vaccine_type,
            'manufacturer' => $request->manufacturer,
            'batch_number' => $request->batch_number,
            'vaccination_date' => $request->vaccination_date,
            'next_due_date' => $request->next_due_date,
            'notes' => $request->notes,
            'updated_at' => now()
        ]);

        return redirect()->route('veterinarian.vaccinations.show', [$petId, $vaccinationId])
            ->with('success', 'Vaccination updated successfully!');
    }
}
