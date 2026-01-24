<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VaccinationController extends Controller
{
    /**
     * Display a listing of vaccinations
     */
    public function index()
    {
        $vaccinations = \App\Models\Vaccination::with(['pet', 'veterinarian'])
            ->orderBy('vaccination_date', 'desc')
            ->paginate(15);

        return view('admin.vaccinations.index', compact('vaccinations'));
    }

    /**
     * Show the form for creating a new vaccination
     */
    public function create()
    {
        $pets = \App\Models\Pet::with('owner.user')->get();
        $veterinarians = \App\Models\User::where('role', 'veterinarian')->get();

        return view('admin.vaccinations.create', compact('pets', 'veterinarians'));
    }

    /**
     * Store a newly created vaccination
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

        \App\Models\Vaccination::create($validated);

        return redirect()->route('admin.vaccinations.index')
            ->with('success', 'Vaccination recorded successfully!');
    }

    /**
     * Display the specified vaccination
     */
    public function show(string $id)
    {
        $vaccination = \App\Models\Vaccination::with(['pet.owner.user', 'veterinarian'])
            ->findOrFail($id);

        return view('admin.vaccinations.show', compact('vaccination'));
    }

    /**
     * Show the form for editing the vaccination
     */
    public function edit(string $id)
    {
        $vaccination = \App\Models\Vaccination::findOrFail($id);
        $pets = \App\Models\Pet::with('owner.user')->get();
        $veterinarians = \App\Models\User::where('role', 'veterinarian')->get();

        return view('admin.vaccinations.edit', compact('vaccination', 'pets', 'veterinarians'));
    }

    /**
     * Update the specified vaccination
     */
    public function update(Request $request, string $id)
    {
        $vaccination = \App\Models\Vaccination::findOrFail($id);

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
     * Delete the specified vaccination
     */
    public function destroy(string $id)
    {
        $vaccination = \App\Models\Vaccination::findOrFail($id);
        $vaccination->delete();

        return redirect()->route('admin.vaccinations.index')
            ->with('success', 'Vaccination deleted successfully!');
    }
}
