<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\PetOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
            try {
                $photo = $request->file('photo');
                $directory = public_path('uploads/pets');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                $filename = $photo->hashName();
                $photo->move($directory, $filename);
                $validated['photo_path'] = 'uploads/pets/' . $filename;
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Pet photo upload failed: ' . $e->getMessage());
                // Continue without photo_path if upload fails
            }
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
        $medicalRecords = MedicalRecord::where('pet_id', $pet->id)
            ->with('veterinarian')
            ->orderByDesc('visit_date')
            ->limit(5)
            ->get();

        $appointments = Appointment::where('pet_id', $pet->id)
            ->orderByDesc('appointment_date')
            ->limit(5)
            ->get();

        return view('admin.pets.show', compact('pet', 'medicalRecords', 'appointments'));
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
        // If owner_id is not submitted (e.g., field is disabled in the form),
        // keep the existing owner_id to avoid validation failures.
        if (!$request->filled('owner_id')) {
            $request->merge(['owner_id' => $pet->owner_id]);
        }

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
            try {
                // Delete old photo if it exists
                if ($pet->photo_path) {
                    File::delete(public_path($pet->photo_path));
                }
                $photo = $request->file('photo');
                $directory = public_path('uploads/pets');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                $filename = $photo->hashName();
                $photo->move($directory, $filename);
                $validated['photo_path'] = 'uploads/pets/' . $filename;
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Pet photo upload failed: ' . $e->getMessage());
                // Continue without updating photo_path if upload fails
            }
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
            File::delete(public_path($pet->photo_path));
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
