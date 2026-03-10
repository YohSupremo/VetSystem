<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PetOwner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PetOwnerController extends Controller
{
    /**
     * Display a listing of pet owners.
     */
    public function index(Request $request)
    {
        $showTrash = $request->boolean('trash');

        $baseQuery = PetOwner::query();

        if ($showTrash) {
            $baseQuery->onlyTrashed();
        }

        $owners = QueryBuilder::for($baseQuery)
            ->with(['user', 'pets'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $term = trim((string) $value);

                    if ($term === '') {
                        return;
                    }

                    $query->where(function ($subQuery) use ($term) {
                        $subQuery->whereHas('user', function ($userQuery) use ($term) {
                            $userQuery->where('first_name', 'like', '%' . $term . '%')
                                ->orWhere('last_name', 'like', '%' . $term . '%')
                                ->orWhere('email', 'like', '%' . $term . '%')
                                ->orWhere('username', 'like', '%' . $term . '%')
                                ->orWhere('contact_number', 'like', '%' . $term . '%');
                        })
                        ->orWhere('emergency_contact_name', 'like', '%' . $term . '%')
                        ->orWhere('emergency_contact_phone', 'like', '%' . $term . '%')
                        ->orWhere('emergency_contact_relationship', 'like', '%' . $term . '%');
                    });
                }),
            ])
            ->orderByDesc('id')
            ->get();

        return view('admin.pet-owners.index', compact('owners', 'showTrash'));
    }

    public function restore(int $id)
    {
        $owner = PetOwner::onlyTrashed()->findOrFail($id);
        $owner->restore();

        return redirect()->route('admin.pet-owners.index', ['trash' => 1])
            ->with('success', 'Pet owner restored successfully.');
    }

    /**
     * Show the form for creating a new pet owner.
     */
    public function create()
    {
        $users = User::where('role', 'registered_user')
            ->whereDoesntHave('petOwner', function ($query) {
                $query->withTrashed();
            })
            ->get();

        return view('admin.pet-owners.create', compact('users'));
    }

    /**
     * Store a newly created pet owner in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('pet_owners', 'user_id'),
            ],
            'notes' => 'nullable|string',
            'preferred_contact_method' => 'nullable|in:email,sms',
            // Single emergency contact fields stored on pet_owners
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
        ]);

        $owner = PetOwner::create([
            'user_id' => $validated['user_id'],
            'notes' => $validated['notes'] ?? null,
            'preferred_contact_method' => $validated['preferred_contact_method'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? null,
        ]);

        // Automatically change user role to pet_owner if this owner already has pets.
        if ($owner->user && $owner->pets()->count() > 0) {
            $owner->user->update(['role' => 'pet_owner']);
        }

        return redirect()->route('admin.pet-owners.show', $owner)
            ->with('success', 'Pet owner created successfully!');
    }

    /**
     * Display the specified pet owner.
     */
    public function show(PetOwner $petOwner)
    {
        $petOwner->load(['user', 'pets']);
        return view('admin.pet-owners.show', compact('petOwner'));
    }

    /**
     * Show the form for editing the specified pet owner.
     */
    public function edit(PetOwner $petOwner)
    {
        $petOwner->load(['user']);
        
        // Get users with registered_user role who are not already assigned as pet owners
        $unassignedUsers = User::where('role', 'registered_user')
            ->whereDoesntHave('petOwner', function ($query) {
                $query->withTrashed();
            })
            ->where('id', '!=', $petOwner->user_id) // Exclude current assigned user
            ->get();
            
        // Get current assigned user to show first
        $currentUser = User::find($petOwner->user_id);
        
        // Combine: current user first, then unassigned users
        $allUsers = collect([$currentUser])->merge($unassignedUsers);
            
        return view('admin.pet-owners.edit', compact('petOwner', 'allUsers'));
    }

    /**
     * Update the specified pet owner in storage.
     */
    public function update(Request $request, PetOwner $petOwner)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:pet_owners,user_id,' . $petOwner->id,
            'notes' => 'nullable|string',
            'preferred_contact_method' => 'nullable|in:email,sms',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
        ]);

        // Update pet owner with new user assignment
        $petOwner->update([
            'user_id' => $validated['user_id'],
            'notes' => $validated['notes'] ?? $petOwner->notes,
            'preferred_contact_method' => $validated['preferred_contact_method'] ?? $petOwner->preferred_contact_method,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? $petOwner->emergency_contact_name,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? $petOwner->emergency_contact_phone,
            'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? $petOwner->emergency_contact_relationship,
        ]);

        // Automatically change user role to pet_owner if they have pets
        $petOwner->load(['user', 'pets']);
        if ($petOwner->user && $petOwner->pets->count() > 0) {
            $petOwner->user->update(['role' => 'pet_owner']);
        }

        return redirect()->route('admin.pet-owners.show', $petOwner)
            ->with('success', 'Pet owner updated successfully!');
    }

    /**
     * Remove the specified pet owner from storage.
     */
    public function destroy(PetOwner $petOwner)
    {
        $petOwner->delete();
        return redirect()->route('admin.pet-owners.index')
            ->with('success', 'Pet owner deleted successfully!');
    }

    /**
     * Search pet owners.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $owners = PetOwner::with('user')
            ->whereHas('user', function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->get();

        return response()->json($owners->map(function ($owner) {
            return [
                'id' => $owner->id,
                'name' => $owner->user->first_name . ' ' . $owner->user->last_name,
                'email' => $owner->user->email,
                'contact' => $owner->user->contact_number,
                'pets_count' => $owner->pets->count(),
            ];
        }));
    }
}
