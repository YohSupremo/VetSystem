<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\PetOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    /**
     * Display a listing of pets.
     */
    public function index()
    {
        $pets = Pet::with('owner.user')->get();
        return view('admin.pets.index', compact('pets'));
    }

    /**
     * Show the form for creating a new pet.
     */
    public function create()
    {
        $owners = PetOwner::with('user')->get();
        return view('admin.pets.create', compact('owners'));
    }

    /**
     * Store a newly created pet in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:pet_owners,id',
            'name' => 'required|string|max:100',
            'species' => 'required|string|max:50',
            'breed' => 'required|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'required|in:male,female,unknown',
            'color' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'microchip_number' => 'nullable|string|unique:pets,microchip_number',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pets', 'public');
            $validated['photo_path'] = $path;
        }

        $pet = Pet::create($validated);

        return redirect()->route('admin.pets.show', $pet)
            ->with('success', 'Pet created successfully!');
    }

    /**
     * Display the specified pet.
     */
    public function show(Pet $pet)
    {
        $pet->load('owner.user');
        return view('admin.pets.show', compact('pet'));
    }

    /**
     * Show the form for editing the specified pet.
     */
    public function edit(Pet $pet)
    {
        $pet->load('owner.user');
        $owners = PetOwner::with('user')->get();
        return view('admin.pets.edit', compact('pet', 'owners'));
    }

    /**
     * Update the specified pet in storage.
     */
    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:pet_owners,id',
            'name' => 'required|string|max:100',
            'species' => 'required|string|max:50',
            'breed' => 'required|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'required|in:male,female,unknown',
            'color' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'microchip_number' => 'nullable|string|unique:pets,microchip_number,' . $pet->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($pet->photo_path) {
                Storage::disk('public')->delete($pet->photo_path);
            }
            $path = $request->file('photo')->store('pets', 'public');
            $validated['photo_path'] = $path;
        }

        $pet->update($validated);

        return redirect()->route('admin.pets.show', $pet)
            ->with('success', 'Pet updated successfully!');
    }

    /**
     * Remove the specified pet from storage.
     */
    public function destroy(Pet $pet)
    {
        // Delete photo if exists
        if ($pet->photo_path) {
            Storage::disk('public')->delete($pet->photo_path);
        }

        $pet->delete();

        return redirect()->route('admin.pets.index')
            ->with('success', 'Pet deleted successfully!');
    }

    /**
     * Search pets.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $pets = Pet::with('owner.user')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('species', 'like', "%{$query}%")
            ->orWhere('breed', 'like', "%{$query}%")
            ->orWhere('microchip_number', 'like', "%{$query}%")
            ->get();

        return response()->json($pets->map(function ($pet) {
            return [
                'id' => $pet->id,
                'name' => $pet->name,
                'species' => $pet->species,
                'breed' => $pet->breed,
                'owner' => $pet->owner->user->first_name . ' ' . $pet->owner->user->last_name,
            ];
        }));
    }
}
