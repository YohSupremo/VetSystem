<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Vaccination;
use App\Models\ChronicCondition;
use App\Models\PetAllergy;
use App\Models\Surgery;
use App\Models\LabRequisition;

class MedicalRecordController extends Controller
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
                'notes' => null
            ]);
        }
        $pets = $petOwner->pets()->with('medicalRecords')->orderBy('name')->get();
        
        return view('customer.medical-records.index', compact('pets'));
    }
    
    public function petRecords($petId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($petId);
        
        // Get medical records with relationships
        $medicalRecords = $pet->medicalRecords()
            ->with(['veterinarian', 'prescriptions'])
            ->orderBy('visit_date', 'desc')
            ->get();
        
        // Get all appointments (completed and scheduled)
        $appointments = $pet->appointments()
            ->with(['veterinarian'])
            ->orderBy('appointment_date', 'desc')
            ->get();
        
        // Get vaccinations separately
        $vaccinations = $pet->vaccinations()
            ->with(['administeredBy', 'inventoryItem'])
            ->orderBy('administered_date', 'desc')
            ->get();
        
        // Get prescriptions separately
        $prescriptions = $pet->prescriptions()
            ->with(['medicalRecord.veterinarian'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get chronic conditions
        $chronicConditions = $pet->chronicConditions()
            ->where('is_active', true)
            ->orderBy('diagnosed_date', 'desc')
            ->get();
        
        // Get pet allergies
        $allergies = $pet->allergies()
            ->where('is_active', true)
            ->orderBy('diagnosed_date', 'desc')
            ->get();
        
        // Get surgeries
        $surgeries = $pet->surgeries()
            ->with(['surgeon', 'surgeryType'])
            ->orderBy('scheduled_date', 'desc')
            ->get();
        
        // Get laboratory tests
        $labTests = $pet->labRequisitions()
            ->with(['test', 'requestedBy'])
            ->orderBy('requested_date', 'desc')
            ->get();
        
        return view('customer.medical-records.pet', compact(
            'pet', 
            'medicalRecords', 
            'appointments',
            'vaccinations', 
            'prescriptions',
            'chronicConditions',
            'allergies',
            'surgeries',
            'labTests'
        ));
    }
    
    public function show($petId, $recordId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($petId);
        
        $medicalRecord = $pet->medicalRecords()
            ->with(['veterinarian', 'prescriptions'])
            ->findOrFail($recordId);
        
        return view('customer.medical-records.show', compact('pet', 'medicalRecord'));
    }
    
    // Chronic Conditions CRUD
    public function storeChronicCondition(Request $request, $petId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($petId);
        
        $validated = $request->validate([
            'condition_name' => 'required|string|max:255',
            'diagnosed_date' => 'required|date',
            'ongoing_treatment' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $pet->chronicConditions()->create([
            'condition_name' => $validated['condition_name'],
            'diagnosed_date' => $validated['diagnosed_date'],
            'ongoing_treatment' => $validated['ongoing_treatment'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);
        
        return redirect()->route('customer.medical-records.pet', $petId)
            ->with('success', 'Chronic condition added successfully.');
    }
    
    public function updateChronicCondition(Request $request, $petId, $conditionId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($petId);
        
        $condition = $pet->chronicConditions()->findOrFail($conditionId);
        
        $validated = $request->validate([
            'condition_name' => 'required|string|max:255',
            'diagnosed_date' => 'required|date',
            'ongoing_treatment' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $condition->update($validated);
        
        return redirect()->route('customer.medical-records.pet', $petId)
            ->with('success', 'Chronic condition updated successfully.');
    }
    
    public function destroyChronicCondition($petId, $conditionId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($petId);
        
        $condition = $pet->chronicConditions()->findOrFail($conditionId);
        $condition->delete();
        
        return redirect()->route('customer.medical-records.pet', $petId)
            ->with('success', 'Chronic condition deleted successfully.');
    }
    
    // Pet Allergies CRUD
    public function storeAllergy(Request $request, $petId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($petId);
        
        $validated = $request->validate([
            'allergen' => 'required|string|max:255',
            'reaction_type' => 'nullable|string|max:255',
            'severity' => 'nullable|in:mild,moderate,severe',
            'diagnosed_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $pet->allergies()->create([
            'allergen' => $validated['allergen'],
            'reaction_type' => $validated['reaction_type'] ?? null,
            'severity' => $validated['severity'] ?? 'mild',
            'diagnosed_date' => $validated['diagnosed_date'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);
        
        return redirect()->route('customer.medical-records.pet', $petId)
            ->with('success', 'Allergy added successfully.');
    }
    
    public function updateAllergy(Request $request, $petId, $allergyId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($petId);
        
        $allergy = $pet->allergies()->findOrFail($allergyId);
        
        $validated = $request->validate([
            'allergen' => 'required|string|max:255',
            'reaction_type' => 'nullable|string|max:255',
            'severity' => 'nullable|in:mild,moderate,severe',
            'diagnosed_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $allergy->update($validated);
        
        return redirect()->route('customer.medical-records.pet', $petId)
            ->with('success', 'Allergy updated successfully.');
    }
    
    public function destroyAllergy($petId, $allergyId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($petId);
        
        $allergy = $pet->allergies()->findOrFail($allergyId);
        $allergy->delete();
        
        return redirect()->route('customer.medical-records.pet', $petId)
            ->with('success', 'Allergy deleted successfully.');
    }
}
