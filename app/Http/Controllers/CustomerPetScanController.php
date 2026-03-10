<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\CageAssignment;
use App\Models\QrScanLog;
use Illuminate\Support\Facades\Auth;

class CustomerPetScanController extends Controller
{
    /**
     * Show customer's caged pets
     */
    public function index()
    {
        $user = Auth::user();
        $petOwner = $user->petOwner;

        if (!$petOwner) {
            return view('customer.pets-scan', ['pets' => collect()]);
        }

        // Get pets that are currently caged
        $pets = Pet::where('owner_id', $petOwner->id)
            ->whereHas('cageAssignments', function ($query) {
                $query->whereNull('check_out_time');
            })
            ->with(['cageAssignments' => function ($query) {
                $query->whereNull('check_out_time')
                    ->with('cage');
            }])
            ->get();

        // Generate scan URLs for each pet
        foreach ($pets as $pet) {
            $pet->scanUrl = route('customer.pet-scan', ['regNumber' => $pet->registration_number]);
        }

        return view('customer.pets-scan', compact('pets'));
    }

    /**
     * Scan pet QR code and log it
     */
    public function scan(string $regNumber)
    {
        $user = Auth::user();
        $petOwner = $user->petOwner;

        // Find pet by registration number
        $pet = Pet::where('registration_number', $regNumber)->firstOrFail();

        // Verify ownership
        if (!$petOwner || $pet->owner_id !== $petOwner->id) {
            abort(403, 'You can only scan your own pets');
        }

        // Get current cage assignment if any
        $assignment = CageAssignment::where('pet_id', $pet->id)
            ->whereNull('check_out_time')
            ->with('cage')
            ->latest()
            ->first();

        // Log the scan
        QrScanLog::safeLog([
            'scan_type' => 'pet',
            'cage_id' => $assignment?->cage_id,
            'pet_id' => $pet->id,
            'scanned_by' => Auth::id(),
            'scan_timestamp' => now(),
        ]);

        return view('customer.pet-scan-result', compact('pet', 'assignment'));
    }

}
