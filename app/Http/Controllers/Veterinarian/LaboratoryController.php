<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaboratoryTest;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;

class LaboratoryController extends Controller
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
            // Show lab tests for specific pet
            $labTests = LaboratoryTest::where(function($query) use ($veterinarian, $petId) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orWhere(function($subQuery) use ($petId) {
                          $subQuery->where('pet_id', $petId)
                                   ->whereNull('veterinarian_id');
                      });
            })
                ->where('pet_id', $petId)
                ->with(['pet', 'pet.owner'])
                ->orderBy('test_date', 'desc')
                ->paginate(10);
        } else {
            // Show all lab tests for this veterinarian
            $labTests = LaboratoryTest::where(function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orWhereNull('veterinarian_id');
            })
                ->with(['pet', 'pet.owner'])
                ->orderBy('test_date', 'desc')
                ->paginate(10);
        }

        return view('veterinarian.laboratory.index', compact('labTests', 'petId'));
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

        return view('veterinarian.laboratory.create', compact('pet', 'veterinarian'));
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
            'test_type' => 'required|string|max:200',
            'test_name' => 'required|string|max:200',
            'specimen_type' => 'required|string|max:100',
            'test_date' => 'required|date',
            'results' => 'required|string|max:2000',
            'interpretation' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        LaboratoryTest::create([
            'pet_id' => $petId,
            'veterinarian_id' => $veterinarian->id,
            'test_type' => $request->test_type,
            'test_name' => $request->test_name,
            'specimen_type' => $request->specimen_type,
            'test_date' => $request->test_date,
            'results' => $request->results,
            'interpretation' => $request->interpretation,
            'notes' => $request->notes,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('veterinarian.patients.show', $petId)
            ->with('success', 'Laboratory test recorded successfully!');
    }

    public function show($petId, $testId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $labTest = LaboratoryTest::where(function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id)
                  ->orWhereNull('veterinarian_id');
        })
            ->where('pet_id', $petId)
            ->with(['pet', 'pet.owner', 'veterinarian'])
            ->findOrFail($testId);

        return view('veterinarian.laboratory.show', compact('labTest'));
    }

    public function edit($petId, $testId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $labTest = LaboratoryTest::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->with(['pet', 'pet.owner'])
            ->findOrFail($testId);

        return view('veterinarian.laboratory.edit', compact('labTest'));
    }

    public function update(Request $request, $petId, $testId)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $labTest = LaboratoryTest::where('veterinarian_id', $veterinarian->id)
            ->where('pet_id', $petId)
            ->findOrFail($testId);

        $request->validate([
            'test_type' => 'required|string|max:200',
            'test_name' => 'required|string|max:200',
            'specimen_type' => 'required|string|max:100',
            'test_date' => 'required|date',
            'results' => 'required|string|max:2000',
            'interpretation' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        $labTest->update([
            'test_type' => $request->test_type,
            'test_name' => $request->test_name,
            'specimen_type' => $request->specimen_type,
            'test_date' => $request->test_date,
            'results' => $request->results,
            'interpretation' => $request->interpretation,
            'notes' => $request->notes,
            'updated_at' => now()
        ]);

        return redirect()->route('veterinarian.laboratory.show', [$petId, $testId])
            ->with('success', 'Laboratory test updated successfully!');
    }
}
