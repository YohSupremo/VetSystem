<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\PetAllergy;
use Illuminate\Http\Request;

class PetAllergyController extends Controller
{
    public function index(Request $request)
    {
        $query = Pet::with(['owner.user'])
            ->withCount([
                'petAllergies as allergy_total_count',
                'petAllergies as allergy_active_count' => function ($sub) {
                    $sub->where('is_active', 1);
                },
            ])
            ->whereHas('petAllergies');

        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhereHas('owner.user', function ($userQuery) use ($q) {
                        $userQuery->where('first_name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('petAllergies', function ($allergyQuery) use ($q) {
                        $allergyQuery->where('allergen', 'like', '%' . $q . '%')
                            ->orWhere('reaction_type', 'like', '%' . $q . '%');
                    });
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'], true)) {
            $statusValue = $request->status === 'active' ? 1 : 0;
            $query->whereHas('petAllergies', function ($allergyQuery) use ($statusValue) {
                $allergyQuery->where('is_active', $statusValue);
            });
        }

        if ($request->filled('severity') && in_array($request->severity, ['mild', 'moderate', 'severe'], true)) {
            $query->whereHas('petAllergies', function ($allergyQuery) use ($request) {
                $allergyQuery->where('severity', $request->severity);
            });
        }

        $groupedPets = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.pet-allergies.index', [
            'groupedPets' => $groupedPets,
            'filters' => [
                'q' => $request->q,
                'status' => $request->status,
                'severity' => $request->severity,
            ],
        ]);
    }

    public function byPet(Pet $pet)
    {
        $pet->load(['owner.user']);

        $allergies = $pet->petAllergies()
            ->with(['medicalRecord:id,visit_date'])
            ->orderByDesc('is_active')
            ->orderByDesc('diagnosed_date')
            ->orderByDesc('id')
            ->get();

        return view('admin.pet-allergies.pet-details', [
            'pet' => $pet,
            'allergies' => $allergies,
        ]);
    }

    public function create(Request $request)
    {
        $pets = Pet::with(['owner.user'])
            ->orderBy('name')
            ->get();

        return view('admin.pet-allergies.create', [
            'pets' => $pets,
            'selectedPetId' => $request->integer('pet_id') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'allergen' => ['required', 'string', 'max:150'],
            'reaction_type' => ['nullable', 'string', 'max:100'],
            'severity' => ['required', 'in:mild,moderate,severe'],
            'diagnosed_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $allergy = PetAllergy::create($validated);

        return redirect()
            ->route('admin.pet-allergies.show', $allergy)
            ->with('success', 'Pet allergy created successfully.');
    }

    public function show(PetAllergy $petAllergy)
    {
        $petAllergy->load(['pet.owner.user']);

        return view('admin.pet-allergies.show', [
            'allergy' => $petAllergy,
        ]);
    }

    public function edit(PetAllergy $petAllergy)
    {
        $petAllergy->load(['pet.owner.user']);

        $pets = Pet::with(['owner.user'])
            ->orderBy('name')
            ->get();

        return view('admin.pet-allergies.edit', [
            'allergy' => $petAllergy,
            'pets' => $pets,
        ]);
    }

    public function update(Request $request, PetAllergy $petAllergy)
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'allergen' => ['required', 'string', 'max:150'],
            'reaction_type' => ['nullable', 'string', 'max:100'],
            'severity' => ['required', 'in:mild,moderate,severe'],
            'diagnosed_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $petAllergy->update($validated);

        return redirect()
            ->route('admin.pet-allergies.show', $petAllergy)
            ->with('success', 'Pet allergy updated successfully.');
    }

    public function destroy(PetAllergy $petAllergy)
    {
        $petAllergy->delete();

        return redirect()
            ->route('admin.pet-allergies.index')
            ->with('success', 'Pet allergy deleted successfully.');
    }
}
