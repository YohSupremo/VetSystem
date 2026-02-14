<?php

namespace App\Http\Controllers\Admin;

use App\Models\CageAssignment;
use App\Models\Pet;
use Illuminate\Http\Request;
use App\Models\Cage;

class BoardingController extends BaseController
{
    /**
     * Display a listing of boarding services.
     */
    public function index()
    {
        $query = CageAssignment::with(['pet.owner.user', 'cage']);
        
        // Search functionality
        if (request('search')) {
            $search = request('search');
            $query->whereHas('pet', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('cage', function ($q) use ($search) {
                $q->where('cage_code', 'like', '%' . $search . '%');
            });
        }
        
        $boardings = $query->orderBy('start_date', 'desc')->get();
        
        // Calculate dashboard stats
        $currentBoardings = CageAssignment::whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();
        
        $availableCages = Cage::where('status', 'available')->count();
        
        $upcomingCheckouts = CageAssignment::whereDate('end_date', '=', now()->toDateString())->count();
        
        $pets = Pet::with('owner')->get();
        $cages = Cage::where('status', 'available')->get();
        
        return view('admin.boarding.index', compact('pets', 'boardings', 'currentBoardings', 'availableCages', 'upcomingCheckouts', 'cages'));
    }

   
    /**
     * Show the form for creating a new boarding service.
     */
    public function create()
    {
        $pets = Pet::with(['owner.user'])
        ->orderBy('name')
        ->get();

        $cages = Cage::with(['currentAssignment'])
        ->where('status', 'available')
        ->orderBy('cage_code')
        ->get();

         return view('admin.boarding.create', compact('pets', 'cages'));

    }

    public function createPass(Request $request)
    {
        $info = $request->validate([
            'cage_id'  => 'required|exists:cages,id',
            'pet_id'   => 'required|exists:pets,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'check_in_time'       => 'nullable|date_format:H:i',
            'check_out_time'      => 'nullable|date_format:H:i',
            'morning_feed_time'   => 'nullable|date_format:H:i',
            'afternoon_feed_time' => 'nullable|date_format:H:i',
            'evening_feed_time'   => 'nullable|date_format:H:i',
            'feeding_notes'       => 'nullable|string',
            'medication_notes'    => 'nullable|string',
            'medication_times'    => 'nullable|string',
            'daily_rate'          => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        $cage = Cage::find($info['cage_id']);

        if ($cage->status !== 'available') {
            return back()->withErrors(['cage_id' => 'This cage is not available.']);
        }

        // Check if pet is already assigned to another cage in overlapping dates
        $overlapping = CageAssignment::where('pet_id', $info['pet_id'])
            ->where(function($query) use ($info) {
                $query->whereBetween('start_date', [$info['start_date'], $info['end_date']])
                      ->orWhereBetween('end_date', [$info['start_date'], $info['end_date']])
                      ->orWhere(function($q) use ($info) {
                          $q->where('start_date', '<=', $info['start_date'])
                            ->where('end_date', '>=', $info['end_date']);
                      });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['pet_id' => 'This pet is already assigned to a cage during the selected dates.']);
        }

        $cage->status = 'occupied';
        $cage->save();

        $assignment = CageAssignment::create([
            'cage_id' => $cage->id,
            'pet_id'  => $info['pet_id'],
            'start_date' => $info['start_date'],
            'end_date'   => $info['end_date'],
            'check_in_time' => $this->combineDateAndTime($info['start_date'], $info['check_in_time'] ?? null),
            'check_out_time' => $this->combineDateAndTime($info['end_date'], $info['check_out_time'] ?? null),
        ]);

        $feeding_times = array_filter([
            $info['morning_feed_time'] ?? null,
            $info['afternoon_feed_time'] ?? null,
            $info['evening_feed_time'] ?? null,
        ]);

        $assignment->update([
            'feeding_schedule' => !empty($feeding_times) ? 'timed' : 'as_needed',
            'feeding_times' => !empty($feeding_times) ? implode(',', $feeding_times) : null,
            'special_diet_notes' => $info['feeding_notes'] ?? null,
            'medication_instructions' => $info['medication_notes'] ?? null,
            'medication_times' => $info['medication_times'] ?? null,
            'daily_rate' => $info['daily_rate'] ?? null,
            'notes' => $info['notes'] ?? null,
        ]);

        return redirect()->route('admin.boarding.index')->with('success', 'Pet successfully assigned to cage and boarding created.');
    }


    /**
     * Store a newly created boarding service in storage.
     */
    public function store(Request $request)
    {
        $info = $request->validate([
            'cage_id'  => 'required|exists:cages,id',
            'pet_id'   => 'required|exists:pets,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'check_in_time'       => 'nullable|date_format:H:i',
            'check_out_time'      => 'nullable|date_format:H:i',
            'feeding_schedule'    => 'nullable|string',
            'feeding_times'       => 'nullable|string',
            'special_diet_notes'  => 'nullable|string',
            'medication_instructions' => 'nullable|string',
            'medication_times'    => 'nullable|string',
            'daily_rate'          => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        $cage = Cage::find($info['cage_id']);

        if ($cage->status !== 'available') {
            return back()->withInput()->withErrors(['cage_id' => 'This cage is not available.']);
        }

        // Check if pet is already assigned to another cage in overlapping dates
        $overlapping = CageAssignment::where('pet_id', $info['pet_id'])
            ->where(function($query) use ($info) {
                $query->whereBetween('start_date', [$info['start_date'], $info['end_date']])
                      ->orWhereBetween('end_date', [$info['start_date'], $info['end_date']])
                      ->orWhere(function($q) use ($info) {
                          $q->where('start_date', '<=', $info['start_date'])
                            ->where('end_date', '>=', $info['end_date']);
                      });
            })
            ->exists();

        if ($overlapping) {
            return back()->withInput()->withErrors(['pet_id' => 'This pet is already assigned to a cage during the selected dates.']);
        }

        $cage->status = 'occupied';
        $cage->save();

        // Create cage assignment with all fields directly from form
        $boarding = CageAssignment::create([
            'cage_id' => $cage->id,
            'pet_id'  => $info['pet_id'],
            'start_date' => $info['start_date'],
            'end_date'   => $info['end_date'],
            'check_in_time' => $this->combineDateAndTime($info['start_date'], $info['check_in_time'] ?? null),
            'check_out_time' => null, // Will be set when pet checks out
            'feeding_schedule' => $info['feeding_schedule'] ?? null,
            'feeding_times' => $info['feeding_times'] ?? null,
            'special_diet_notes' => $info['special_diet_notes'] ?? null,
            'medication_instructions' => $info['medication_instructions'] ?? null,
            'medication_times' => $info['medication_times'] ?? null,
            'daily_rate' => $info['daily_rate'] ?? null,
            'notes' => $info['notes'] ?? null,
        ]);

        return redirect()->route('admin.boarding.index')->with('success', 'Pet successfully assigned to cage and boarding created.');
    }


    /**
     * Display the specified boarding service.
     */
    public function show($id)
    {
        $boarding = CageAssignment::with(['pet.owner.user', 'cage'])->findOrFail($id);
        return view('admin.boarding.show', compact('boarding'));
    }

    /**
     * Show the form for editing the specified boarding service.
     */
    public function edit($id)
    {
        $boarding = CageAssignment::with(['pet.owner.user', 'cage'])->findOrFail($id);
        $pets = Pet::with(['owner.user'])->orderBy('name')->get();
        $cages = Cage::where('status', 'available')->orWhere('id', $boarding->cage_id)->orderBy('cage_code')->get();
        
        return view('admin.boarding.edit', compact('boarding', 'pets', 'cages'));
    }

    /**
     * Update the specified boarding service in storage.
     */
    public function update(Request $request, $id)
    {
        $boarding = CageAssignment::findOrFail($id);
        
        $info = $request->validate([
            'cage_id'  => 'required|exists:cages,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'check_in_time'       => 'nullable|date_format:H:i',
            'check_out_time'      => 'nullable|date_format:H:i',
            'feeding_schedule'    => 'nullable|string',
            'feeding_times'       => 'nullable|string',
            'special_diet_notes'  => 'nullable|string',
            'medication_instructions' => 'nullable|string',
            'medication_times'    => 'nullable|string',
            'daily_rate'          => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        // If cage changed, update old cage status and new cage status
        if ($boarding->cage_id != $info['cage_id']) {
            $oldCage = Cage::find($boarding->cage_id);
            if ($oldCage) {
                $oldCage->status = 'available';
                $oldCage->save();
            }

            $newCage = Cage::find($info['cage_id']);
            if ($newCage && $newCage->status !== 'occupied') {
                $newCage->status = 'occupied';
                $newCage->save();
            }
        }

        // Determine dates: if provided, use new; otherwise keep existing
        $startDate = $info['start_date'] ?? $boarding->start_date;
        $endDate = $info['end_date'] ?? $boarding->end_date;

        // Determine check_in_time: if provided, combine with start_date; otherwise keep existing
        $checkInTime = isset($info['check_in_time']) 
            ? $this->combineDateAndTime($startDate, $info['check_in_time'])
            : $boarding->check_in_time;

        // Determine check_out_time: if provided, combine with end_date; otherwise keep existing
        $checkOutTime = isset($info['check_out_time']) 
            ? $this->combineDateAndTime($endDate, $info['check_out_time'])
            : $boarding->check_out_time;

        // Update cage assignment with all fields directly from form
        $boarding->update([
            'cage_id' => $info['cage_id'],
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'feeding_schedule' => $info['feeding_schedule'] ?? null,
            'feeding_times' => $info['feeding_times'] ?? null,
            'special_diet_notes' => $info['special_diet_notes'] ?? null,
            'medication_instructions' => $info['medication_instructions'] ?? null,
            'medication_times' => $info['medication_times'] ?? null,
            'daily_rate' => $info['daily_rate'] ?? null,
            'notes' => $info['notes'] ?? null,
        ]);
        
        return redirect()->route('admin.boarding.index')->with('success', 'Boarding updated successfully.');
    }

    /**
     * Remove the specified boarding service from storage.
     */
    public function destroy($id)
    {
        $boarding = CageAssignment::findOrFail($id);
        
        // Release the cage - check if there are other active assignments first
        $cage = Cage::find($boarding->cage_id);
        if ($cage) {
            $hasOtherAssignments = CageAssignment::where('cage_id', $cage->id)
                ->where('id', '!=', $boarding->id)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->exists();
            
            if (!$hasOtherAssignments) {
                $cage->status = 'available';
                $cage->save();
            }
        }

        $boarding->delete();

        return redirect()->route('admin.boarding.index')->with('success', 'Boarding deleted successfully.');
    }

    private function combineDateAndTime($date, ?string $time)
    {
        if (!$time) {
            return null;
        }

        // Ensure date is just Y-m-d (take first 10 chars)
        // This handles cases where $date is a full datetime string (e.g. from DB)
        $date = substr((string)$date, 0, 10);

        return \Carbon\Carbon::parse($date . ' ' . $time);
    }
}
