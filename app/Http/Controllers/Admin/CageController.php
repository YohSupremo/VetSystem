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
        $cages = Cage::orderBy('cage_code')->paginate(10);
        return view('admin.cages.index', compact('cages'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cage = Cage::findOrFail($id);
        
        // Find current assignment if any
        $assignment = CageAssignment::where('cage_id', $cage->id)
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
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

        // Find active assignment
        // Logic: End date is null OR end date is in future
        $assignment = CageAssignment::where('cage_id', $cage->id)
            ->where('start_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->with(['pet.owner', 'pet.medicalRecords' => function($query) {
                $query->latest()->limit(5);
            }])
            ->latest()
            ->first();

        return view('admin.cages.scan', compact('cage', 'assignment'));
    }
}
