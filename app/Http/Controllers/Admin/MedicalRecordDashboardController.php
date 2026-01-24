<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\PetVaccination;
use App\Models\Prescription;
use App\Models\Surgery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalRecordDashboardController extends Controller
{
    public function index()
    {
        $pets = Pet::with(['owner.user'])->get();
        
        // Get recent medical records with related data
        $medicalRecords = MedicalRecord::with(['pet.owner.user', 'veterinarian'])
            ->orderBy('visit_date', 'desc')
            ->paginate(10);
            
        // Get upcoming vaccinations
        $vaccinations = PetVaccination::with(['pet', 'vaccine', 'administeredBy'])
            ->orderBy('next_due_date', 'asc')
            ->limit(20)
            ->get();
            
        // Get recent prescriptions
        $prescriptions = Prescription::with(['medicalRecord.pet'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            
        // Get upcoming surgeries
        $surgeries = Surgery::with(['pet.owner.user', 'surgeon'])
            ->orderBy('scheduled_date', 'asc')
            ->limit(10)
            ->get();
        
        return view('admin.medical-records.dashboard', compact(
            'pets',
            'medicalRecords',
            'vaccinations',
            'prescriptions',
            'surgeries'
        ));
    }
}
