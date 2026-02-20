<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\PetOwner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    private function authenticateUser()
    {
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $user = User::where('username', $username)->first();
        if (!$user || ($user->role !== 'pet_owner' && $user->role !== 'registered_user')) {
            return redirect('/login')->with('error', 'Access denied');
        }

        return $user;
    }

    private function incidentTypes(): array
    {
        return [
            'pet_injury' => 'Pet Injury',
            'pet_illness' => 'Pet Illness',
            'pet_escape' => 'Pet Escape',
            'pet_aggression' => 'Pet Aggression',
            'staff_injury' => 'Staff Injury',
            'visitor_injury' => 'Visitor Injury',
            'medication_error' => 'Medication Error',
            'equipment_failure' => 'Equipment Failure',
            'facility_damage' => 'Facility Damage',
            'other' => 'Other',
        ];
    }

    private function severityOptions(): array
    {
        return [
            'minor' => 'Minor',
            'moderate' => 'Moderate',
            'severe' => 'Severe',
            'critical' => 'Critical',
        ];
    }

    private function generateIncidentNumber(): string
    {
        $prefix = 'INC-' . now()->format('Y');
        $sequence = Incident::where('incident_number', 'like', $prefix . '%')->count() + 1;

        do {
            $number = sprintf('%s-%06d', $prefix, $sequence);
            $sequence++;
        } while (Incident::where('incident_number', $number)->exists());

        return $number;
    }

    public function index()
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }

        view()->share('user', $user);

        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null,
            ]);
        }

        $petIds = $petOwner->pets()->pluck('id');

        $incidents = Incident::with('pet')
            ->whereIn('pet_id', $petIds)
            ->orderBy('incident_date', 'desc')
            ->get();

        return view('customer.incidents.index', compact('incidents'));
    }

    public function create()
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }

        view()->share('user', $user);

        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null,
            ]);
        }

        $pets = $petOwner->pets()->orderBy('name')->get();

        return view('customer.incidents.create', [
            'pets' => $pets,
            'incidentTypes' => $this->incidentTypes(),
            'severityOptions' => $this->severityOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }

        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'incident_date' => 'required|date',
            'incident_type' => 'required|in:pet_injury,pet_illness,pet_escape,pet_aggression,staff_injury,visitor_injury,medication_error,equipment_failure,facility_damage,other',
            'severity' => 'required|in:minor,moderate,severe,critical',
            'location' => 'required|string|max:150',
            'description' => 'required|string',
            'immediate_action_taken' => 'nullable|string',
        ]);

        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner || !$petOwner->pets()->where('id', $validated['pet_id'])->exists()) {
            return redirect()->back()->withInput()->with('error', 'Selected pet is not available.');
        }

        Incident::create([
            'incident_number' => $this->generateIncidentNumber(),
            'incident_date' => Carbon::parse($validated['incident_date']),
            'incident_type' => $validated['incident_type'],
            'severity' => $validated['severity'],
            'pet_id' => $validated['pet_id'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'immediate_action_taken' => $validated['immediate_action_taken'] ?? null,
            'status' => 'open',
            'reported_by' => $user->id,
            'reported_at' => now(),
        ]);

        return redirect()->route('customer.incidents.index')
            ->with('success', 'Incident report submitted. Our team will review it shortly.');
    }

    public function show($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }

        view()->share('user', $user);

        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $incident = Incident::with(['pet', 'reportedBy', 'incidentNotes.addedBy'])->findOrFail($id);

        if (!$petOwner || !$incident->pet || !$petOwner->pets()->where('id', $incident->pet_id)->exists()) {
            abort(403);
        }

        return view('customer.incidents.show', compact('incident'));
    }
}
