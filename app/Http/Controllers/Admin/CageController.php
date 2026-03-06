<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cage;
use App\Models\CageAssignment;
use App\Models\QrScanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CageController extends Controller
{
    private function activeAssignmentQuery(int $cageId)
    {
        $now = now();

        return CageAssignment::where('cage_id', $cageId)
            ->where(function ($query) use ($now) {
                $query->whereNull('check_in_time')
                    ->whereDate('start_date', '<=', $now->toDateString())
                    ->orWhere('check_in_time', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('check_out_time')
                    ->whereDate('end_date', '>=', $now->toDateString())
                    ->orWhere('check_out_time', '>', $now);
            });
    }

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
        $assignment = $this->activeAssignmentQuery($cage->id)
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
        $assignment = $this->activeAssignmentQuery($cage->id)
            ->with(['pet.owner', 'pet.medicalRecords' => function($query) {
                $query->latest()->limit(5);
            }])
            ->latest()
            ->first();

        // Log the scan
        QrScanLog::create([
            'scan_type' => 'cage',
            'cage_id' => $cage->id,
            'pet_id' => $assignment?->pet_id,
            'scanned_by' => Auth::id(),
            'scan_timestamp' => now(),
        ]);

        return view('admin.cages.scan', compact('cage', 'assignment'));
    }

    /**
     * Release current pet from cage.
     */
    public function release(string $id)
    {
        $cage = Cage::findOrFail($id);

        $assignment = $this->activeAssignmentQuery($cage->id)->latest()->first();

        if (!$assignment) {
            return redirect()->route('admin.cages.index')
                ->with('warning', 'No active cage assignment to release.');
        }

        $assignment->update([
            'check_out_time' => now(),
        ]);

        $cage->syncStatus();

        return redirect()->route('admin.cages.index')
            ->with('success', 'Pet released successfully. Cage is now available.');
    }
}
