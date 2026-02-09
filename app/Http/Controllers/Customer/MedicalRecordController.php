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

class MedicalRecordController extends Controller
{
    private function authenticateUser()
    {
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }
        
        $user = User::where('username', $username)->first();
        if (!$user || $user->role !== 'pet_owner') {
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
            ->with(['administeredBy'])
            ->orderBy('vaccination_date', 'desc')
            ->get();
        
        // Get prescriptions separately
        $prescriptions = $pet->prescriptions()
            ->with('prescribedBy')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.medical-records.pet', compact(
            'pet', 
            'medicalRecords', 
            'appointments',
            'vaccinations', 
            'prescriptions'
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
}
