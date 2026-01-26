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
        $pets = Pet::with('owner.user', 'vaccinations')
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
        $veterinarians = User::where('role', 'veterinarian')->get();

        return view('admin.vaccinations.create', compact('pets', 'veterinarians'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccination_date' => 'required|date',
            'veterinarian_id' => 'nullable|exists:users,id',
            'next_due_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
            'route_of_administration' => 'nullable|in:intramuscular,subcutaneous,intranasal,oral',
            'site_of_injection' => 'nullable|string|max:255',
            'adverse_reactions' => 'nullable|string',
            'notes' => 'nullable|string',
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
        $vaccination = Vaccination::with(['pet.owner.user', 'veterinarian'])
            ->findOrFail($id);

        return view('admin.vaccinations.show', compact('vaccination'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vaccination = Vaccination::findOrFail($id);
        $pets = Pet::with('owner.user')->get();
        $veterinarians = User::where('role', 'veterinarian')->get();

        return view('admin.vaccinations.edit', compact('vaccination', 'pets', 'veterinarians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vaccination = Vaccination::findOrFail($id);

        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'vaccination_date' => 'required|date',
            'veterinarian_id' => 'nullable|exists:users,id',
            'next_due_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
            'route_of_administration' => 'nullable|in:intramuscular,subcutaneous,intranasal,oral',
            'site_of_injection' => 'nullable|string|max:255',
            'adverse_reactions' => 'nullable|string',
            'notes' => 'nullable|string',
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
        $pet = Pet::with('vaccinations.veterinarian')->findOrFail($petId);
        $vaccinations = $pet->vaccinations()->orderBy('vaccination_date', 'desc')->paginate(10);

        return view('admin.vaccinations.pet', compact('pet', 'vaccinations'));
    }
}
