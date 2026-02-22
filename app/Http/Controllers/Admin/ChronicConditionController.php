<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChronicCondition;
use App\Models\Pet;
use Illuminate\Http\Request;

class ChronicConditionController extends Controller
{
    public function create(Request $request)
    {
        $pets = Pet::with(['owner.user'])
            ->orderBy('name')
            ->get();

        return view('admin.chronic-conditions.create', [
            'pets' => $pets,
            'selectedPetId' => $request->integer('pet_id') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'condition_name' => ['required', 'string', 'max:150'],
            'diagnosed_date' => ['nullable', 'date'],
            'ongoing_treatment' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $condition = ChronicCondition::create($validated);

        return redirect()
            ->route('admin.chronic-conditions.show', $condition)
            ->with('success', 'Chronic condition created successfully.');
    }

    public function index(Request $request)
    {
        $query = Pet::with(['owner.user'])
            ->withCount([
                'chronicConditions as chronic_total_count',
                'chronicConditions as chronic_active_count' => function ($sub) {
                    $sub->where('is_active', 1);
                },
            ])
            ->whereHas('chronicConditions');

        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhereHas('owner.user', function ($userQuery) use ($q) {
                        $userQuery->where('first_name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('chronicConditions', function ($conditionQuery) use ($q) {
                        $conditionQuery->where('condition_name', 'like', '%' . $q . '%');
                    });
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'], true)) {
            $statusValue = $request->status === 'active' ? 1 : 0;
            $query->whereHas('chronicConditions', function ($conditionQuery) use ($statusValue) {
                $conditionQuery->where('is_active', $statusValue);
            });
        }

        $groupedPets = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.chronic-conditions.index', [
            'groupedPets' => $groupedPets,
            'filters' => [
                'q' => $request->q,
                'status' => $request->status,
            ],
        ]);
    }

    public function byPet(Pet $pet)
    {
        $pet->load(['owner.user']);

        $conditions = $pet->chronicConditions()
            ->with(['medicalRecord:id,visit_date'])
            ->orderByDesc('is_active')
            ->orderByDesc('diagnosed_date')
            ->orderByDesc('id')
            ->get();

        return view('admin.chronic-conditions.pet', [
            'pet' => $pet,
            'conditions' => $conditions,
        ]);
    }

    public function show(ChronicCondition $chronicCondition)
    {
        $chronicCondition->load(['pet.owner.user']);

        return view('admin.chronic-conditions.show', [
            'condition' => $chronicCondition,
        ]);
    }

    public function edit(ChronicCondition $chronicCondition)
    {
        $chronicCondition->load(['pet.owner.user']);

        $pets = Pet::with(['owner.user'])
            ->orderBy('name')
            ->get();

        return view('admin.chronic-conditions.edit', [
            'condition' => $chronicCondition,
            'pets' => $pets,
        ]);
    }

    public function update(Request $request, ChronicCondition $chronicCondition)
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'condition_name' => ['required', 'string', 'max:150'],
            'diagnosed_date' => ['nullable', 'date'],
            'ongoing_treatment' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $chronicCondition->update($validated);

        return redirect()
            ->route('admin.chronic-conditions.show', $chronicCondition)
            ->with('success', 'Chronic condition updated successfully.');
    }

    public function destroy(ChronicCondition $chronicCondition)
    {
        $chronicCondition->delete();

        return redirect()
            ->route('admin.chronic-conditions.index')
            ->with('success', 'Chronic condition deleted successfully.');
    }
}
