<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cage;
use App\Models\CageAssignment;
use Illuminate\Http\Request;

class CageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Sync all cages to ensure their status matches actual assignments
        $allCages = Cage::all();
        foreach ($allCages as $cage) {
            $cage->syncStatus();
        }
        
        $cages = Cage::orderBy('cage_code')->paginate(10);
        return view('admin.cages.index', compact('cages'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cage = Cage::findOrFail($id);
        
        // Sync cage status based on active assignments
        $cage->syncStatus();
        
        // Find current assignment if any
        $assignment = CageAssignment::where('cage_id', $cage->id)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->with(['pet.owner', 'pet.medicalRecords'])
            ->latest()
            ->first();

        // Calculate QR Code URL
        // We use the route('admin.cages.scan', $cage->cage_code) to generate the URL
        $scanUrl = route('admin.cages.scan', ['code' => $cage->cage_code]);

        return view('admin.cages.show', compact('cage', 'assignment', 'scanUrl'));
    }

    /**
     * Scan the QR code and display contents
     */
    public function scan(string $code)
    {
        $cage = Cage::where('cage_code', $code)->firstOrFail();

        // Sync cage status based on active assignments
        $cage->syncStatus();

        // Find active assignment (only if currently active)
        // Logic: start_date <= today AND end_date >= today
        $assignment = CageAssignment::where('cage_id', $cage->id)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->with(['pet.owner', 'pet.medicalRecords' => function($query) {
                $query->latest()->limit(5);
            }])
            ->latest()
            ->first();

        return view('admin.cages.scan', compact('cage', 'assignment'));
    }
}
