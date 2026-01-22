<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PetOwner;
use App\Models\User;
use App\Models\OwnerEmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PetOwnerController extends Controller
{
    /**
     * Display a listing of pet owners.
     */
    public function index()
    {
        $owners = PetOwner::with('user', 'emergencyContacts', 'pets')->get();
        return view('admin.pet-owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new pet owner.
     */
    public function create()
    {
        $users = User::where('role', 'pet_owner')->doesntHave('petOwner')->get();
        return view('admin.pet-owners.create', compact('users'));
    }

    /**
     * Store a newly created pet owner in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:pet_owners,user_id',
            'notes' => 'nullable|string',
            'emergency_contacts' => 'nullable|array',
            'emergency_contacts.*.contact_name' => 'required_with:emergency_contacts|string',
            'emergency_contacts.*.contact_number' => 'required_with:emergency_contacts|string',
        ]);

        $owner = PetOwner::create([
            'user_id' => $validated['user_id'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create emergency contacts
        if (!empty($validated['emergency_contacts'])) {
            foreach ($validated['emergency_contacts'] as $contact) {
                $owner->emergencyContacts()->create($contact);
            }
        }

        return redirect()->route('admin.pet-owners.show', $owner)
            ->with('success', 'Pet owner created successfully!');
    }

    /**
     * Display the specified pet owner.
     */
    public function show(PetOwner $petOwner)
    {
        $petOwner->load('user', 'emergencyContacts', 'pets');
        return view('admin.pet-owners.show', compact('petOwner'));
    }

    /**
     * Show the form for editing the specified pet owner.
     */
    public function edit(PetOwner $petOwner)
    {
        $petOwner->load('user', 'emergencyContacts');
        return view('admin.pet-owners.edit', compact('petOwner'));
    }

    /**
     * Update the specified pet owner in storage.
     */
    public function update(Request $request, PetOwner $petOwner)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($petOwner->user_id)],
            'contact_number' => 'required|string',
            'address' => 'required|string',
            'notes' => 'nullable|string',
            'emergency_contacts' => 'nullable|array',
            'emergency_contacts.*.contact_name' => 'required_with:emergency_contacts|string',
            'emergency_contacts.*.contact_number' => 'required_with:emergency_contacts|string',
        ]);

        // Update user info
        $petOwner->user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'address' => $validated['address'],
        ]);

        // Update owner notes
        $petOwner->update(['notes' => $validated['notes'] ?? null]);

        // Update emergency contacts
        $petOwner->emergencyContacts()->delete();
        if (!empty($validated['emergency_contacts'])) {
            foreach ($validated['emergency_contacts'] as $contact) {
                $petOwner->emergencyContacts()->create($contact);
            }
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
