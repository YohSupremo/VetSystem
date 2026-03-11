<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\PetOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PetController extends Controller
{
    /**
     * Display a listing of pets.
     */
    public function index(Request $request)
    {
        $showTrash = $request->boolean('trash');

        $baseQuery = Pet::query();

        if ($showTrash) {
            $baseQuery->onlyTrashed();
        }

        $pets = QueryBuilder::for($baseQuery)
            ->with('owner.user')
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $term = trim((string) $value);

                    if ($term === '') {
                        return;
                    }

                    $query->where(function ($subQuery) use ($term) {
                        $subQuery->where('name', 'like', '%' . $term . '%')
                            ->orWhere('species', 'like', '%' . $term . '%')
                            ->orWhere('breed', 'like', '%' . $term . '%')
                            ->orWhere('registration_number', 'like', '%' . $term . '%')
                            ->orWhereHas('owner.user', function ($ownerUserQuery) use ($term) {
                                $ownerUserQuery->where('first_name', 'like', '%' . $term . '%')
                                    ->orWhere('last_name', 'like', '%' . $term . '%')
                                    ->orWhere('email', 'like', '%' . $term . '%');
                            });
                    });
                }),
            ])
            ->orderByDesc('id')
            ->get();

        return view('admin.pets.index', compact('pets', 'showTrash'));
    }

    public function restore(int $id)
    {
        $pet = Pet::onlyTrashed()->findOrFail($id);
        $pet->restore();

        return redirect()->route('admin.pets.index', ['trash' => 1])
            ->with('success', 'Pet restored successfully.');
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
            'registration_number' => 'nullable|string|unique:pets,registration_number',
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

        // Automatically change owner's user role to pet_owner since they now have pets
        $pet->load('owner.user');
        if ($pet->owner && $pet->owner->user) {
            $pet->owner->user->update(['role' => 'pet_owner']);
        }

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
            'registration_number' => 'nullable|string|unique:pets,registration_number,' . $pet->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            try {
                \Log::info('Admin Pet update: photo file detected', ['pet_id' => $pet->id]);
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
                \Log::info('Admin Pet update: photo moved', ['path' => $validated['photo_path']]);
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Pet photo upload failed: ' . $e->getMessage());
                // Continue without updating photo_path if upload fails
            }
        }

        \Log::info('Admin Pet update: before update', ['pet_id' => $pet->id, 'photo_path' => $pet->photo_path]);
        $pet->update($validated);
        \Log::info('Admin Pet update: after update', ['pet_id' => $pet->id, 'photo_path' => $pet->photo_path]);

        return redirect()->route('admin.pets.show', $pet)
            ->with('success', 'Pet updated successfully!');
    }

    /**
     * Remove the specified pet from storage.
     */
    public function destroy(Pet $pet)
    {
        $pet->delete();

        $pet->load('owner.user');
        if ($pet->owner && $pet->owner->user && $pet->owner->pets->count() === 0) {
            $pet->owner->user->update(['role' => 'registered_user']);
        }   

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
            ->orWhere('registration_number', 'like', "%{$query}%")
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
