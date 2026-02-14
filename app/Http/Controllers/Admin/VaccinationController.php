<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\User;
use Illuminate\Http\Request;

class VaccinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pets = Pet::with(['owner.user', 'vaccinations' => function($query) {
            $query->orderBy('administered_date', 'desc');
        }])
            ->has('vaccinations')
            ->paginate(10);

        return view('admin.vaccinations.index', compact('pets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pets = Pet::with('owner.user')->get();
        $vaccines = \App\Models\Vaccine::where('is_active', true)->orderBy('vaccine_name')->get();
        $veterinarians = User::where('role', 'veterinarian')->orderBy('first_name')->get();
        $selectedPetId = request()->query('pet_id');

        return view('admin.vaccinations.create', compact('pets', 'vaccines', 'veterinarians', 'selectedPetId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'administered_date' => 'required|date',
            'administered_by' => 'required|exists:users,id',
            'next_due_date' => 'nullable|date|after_or_equal:administered_date',
            'batch_number' => 'nullable|string|max:255',
            'dose_number' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date|after_or_equal:administered_date',
            'notes' => 'nullable|string',
        ], [
            'administered_by.required' => 'Please select who administered the vaccine.',
            'administered_by.exists' => 'The selected veterinarian is invalid.',
        ]);

        Vaccination::create($validated);

        return redirect()->route('admin.vaccinations.index')
            ->with('success', 'Vaccination recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vaccination = Vaccination::with(['pet.owner.user', 'vaccine', 'administeredBy'])
            ->findOrFail($id);

        return view('admin.vaccinations.show', compact('vaccination'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vaccination = Vaccination::with(['pet.owner.user', 'vaccine', 'administeredBy'])->findOrFail($id);
        $pets = Pet::with('owner.user')->get();
        $vaccines = \App\Models\Vaccine::where('is_active', true)->orderBy('vaccine_name')->get();
        $veterinarians = User::where('role', 'veterinarian')->orderBy('first_name')->get();

        return view('admin.vaccinations.edit', compact('vaccination', 'pets', 'vaccines', 'veterinarians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vaccination = Vaccination::findOrFail($id);

        $validated = $request->validate([
            'vaccine_id' => 'required|exists:vaccines,id',
            'administered_date' => 'required|date',
            'administered_by' => 'required|exists:users,id',
            'next_due_date' => 'nullable|date|after_or_equal:administered_date',
            'batch_number' => 'nullable|string|max:255',
            'dose_number' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date|after_or_equal:administered_date',
            'notes' => 'nullable|string',
        ], [
            'administered_by.required' => 'Please select who administered the vaccine.',
            'administered_by.exists' => 'The selected veterinarian is invalid.',
        ]);

        $vaccination->update($validated);

        return redirect()->route('admin.vaccinations.show', $vaccination->id)
            ->with('success', 'Vaccination updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vaccination = Vaccination::findOrFail($id);
        $vaccination->delete();

        return redirect()->route('admin.vaccinations.index')
            ->with('success', 'Vaccination deleted successfully!');
    }

    /**
     * Display vaccinations for a specific pet.
     */
    public function byPet($petId)
    {
        $pet = Pet::findOrFail($petId);
        $vaccinations = $pet->vaccinations()
            ->with(['vaccine', 'administeredBy'])
            ->orderBy('administered_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.vaccinations.pet', compact('pet', 'vaccinations'));
    }
}
