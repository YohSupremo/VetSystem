<?php

namespace App\Http\Controllers\Admin;

use App\Models\CageAssignment;
use App\Models\Pet;
use App\Models\FeedingSchedule;
use App\Models\MedicationInstruction;
use Illuminate\Http\Request;
use App\Models\Cage;
use Symfony\Contracts\Service\Attribute\Required;
class BoardingController extends BaseController
{
    /**
     * Display a listing of boarding services.
     */
    public function index()
    {
        $query = CageAssignment::with(['petAssigned.owner.user', 'cageAssigned']);
        
        // Search functionality
        if (request('search')) {
            $search = request('search');
            $query->whereHas('petAssigned', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('cageAssigned', function ($q) use ($search) {
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
            'morning_feed_time'   => 'nullable|date_format:H:i',
            'afternoon_feed_time' => 'nullable|date_format:H:i',
            'evening_feed_time'   => 'nullable|date_format:H:i',
            // Notes are required because the database column is NOT NULL
            'feeding_notes'       => 'required|string',
            'medication_notes'    => 'nullable|string',
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
        ]);

        $feeding_times = array_filter([
            $info['morning_feed_time'] ?? null,
            $info['afternoon_feed_time'] ?? null,
            $info['evening_feed_time'] ?? null,
        ]);

        $feeding_schedule = !empty($feeding_times) 
            ? implode(',', $feeding_times) 
            : 'As_Needed';

        // Always store non-empty notes to satisfy NOT NULL constraint
        $feeding_notes = trim($info['feeding_notes'] ?? ''); 
        if ($feeding_notes === '') {
            $feeding_notes = 'No specific notes provided.';
        }
                       

        FeedingSchedule::updateOrCreate(
            ['pet_id' => $info['pet_id']],
            [
                'schedule' => $feeding_schedule,
                'notes'    => $feeding_notes,
            ]
        );

        // Store medication notes on assignment and medication instructions if provided
        $medicationNotes = trim($info['medication_notes'] ?? '');
        if ($medicationNotes !== '') {
            $assignment->update([
                'medication_notes' => $medicationNotes,
            ]);

            MedicationInstruction::updateOrCreate(
                ['pet_id' => $info['pet_id']],
                ['instructions' => $medicationNotes]
            );
        }

        return redirect()->route('admin.boarding.index')->with('success', 'Pet successfully assigned to cage, feeding schedule and medication instructions saved.');
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
            'morning_feed_time'   => 'nullable|date_format:H:i',
            'afternoon_feed_time' => 'nullable|date_format:H:i',
            'evening_feed_time'   => 'nullable|date_format:H:i',
            // Notes are required because the database column is NOT NULL
            'feeding_notes'       => 'required|string',
            'medication_notes'    => 'nullable|string',
            'special_instructions' => 'nullable|string', // legacy support
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

        $boarding = CageAssignment::create([
            'cage_id' => $cage->id,
            'pet_id'  => $info['pet_id'],
            'start_date' => $info['start_date'],
            'end_date'   => $info['end_date'],
        ]);

        // Store feeding schedule
        $feeding_times = array_filter([
            $info['morning_feed_time'] ?? null,
            $info['afternoon_feed_time'] ?? null,
            $info['evening_feed_time'] ?? null,
        ]);

        $feeding_schedule = !empty($feeding_times) 
            ? implode(',', $feeding_times) 
            : 'As_Needed';

        // Always store non-empty notes to satisfy NOT NULL constraint
        $feeding_notes = trim($info['feeding_notes'] ?? '');
        if ($feeding_notes === '') {
            $feeding_notes = 'No specific notes provided.';
        }
                    

        FeedingSchedule::updateOrCreate(
            ['pet_id' => $info['pet_id']],
            [
                'schedule' => $feeding_schedule,
                'notes'    => $feeding_notes,
            ]
        );

        // Store medication notes on cage assignment and medication instructions if provided
        $medicationNotes = trim($info['medication_notes'] ?? ($info['special_instructions'] ?? ''));
        if ($medicationNotes !== '') {
            $boarding->update([
                'medication_notes' => $medicationNotes,
            ]);

            MedicationInstruction::updateOrCreate(
                ['pet_id' => $info['pet_id']],
                ['instructions' => $medicationNotes]
            );
        }

        return redirect()->route('admin.boarding.index')->with('success', 'Pet successfully assigned to cage and boarding created.');
    }


    /**
     * Display the specified boarding service.
     */
    public function show($id)
    {
        $boarding = CageAssignment::with(['petAssigned.owner.user', 'cageAssigned', 'feedingSchedule', 'medicationInstruction'])->findOrFail($id);
        return view('admin.boarding.show', compact('boarding'));
    }

    /**
     * Show the form for editing the specified boarding service.
     */
    public function edit($id)
    {
        $boarding = CageAssignment::with(['petAssigned.owner.user', 'cageAssigned', 'feedingSchedule', 'medicationInstruction'])->findOrFail($id);
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
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'check_in_time'       => 'nullable|date_format:H:i',
            'check_out_time'      => 'nullable|date_format:H:i',
            'morning_feed_time'   => 'nullable|date_format:H:i',
            'afternoon_feed_time' => 'nullable|date_format:H:i',
            'evening_feed_time'   => 'nullable|date_format:H:i',
            // Notes are required because the database column is NOT NULL
            'feeding_notes'       => 'required|string',
            'medication_notes'    => 'nullable|string',
            'special_instructions' => 'nullable|string', // legacy support
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

        // Update cage assignment (including medication notes if any)
        $updateData = [
            'cage_id' => $info['cage_id'],
            'start_date' => $info['start_date'],
            'end_date'   => $info['end_date'],
        ];
        if (!empty($info['special_instructions'])) {
            $updateData['medication_notes'] = $info['special_instructions'];
        }
        $boarding->update($updateData);

        // Update feeding schedule
        $feeding_times = array_filter([
            $info['morning_feed_time'] ?? null,
            $info['afternoon_feed_time'] ?? null,
            $info['evening_feed_time'] ?? null,
        ]);

        $feeding_schedule = !empty($feeding_times) 
            ? implode(',', $feeding_times) 
            : 'As_Needed';

        $feeding_notes = trim($info['feeding_notes'] ?? '');
        if ($feeding_notes === '') {
            $feeding_notes = 'No specific notes provided.';
        }

        FeedingSchedule::updateOrCreate(
            ['pet_id' => $boarding->pet_id],
            [
                'schedule' => $feeding_schedule,
                'notes'    => $feeding_notes,
            ]
        );

        // Update medication notes on cage assignment and medication instructions
        $medicationNotes = trim($info['medication_notes'] ?? ($info['special_instructions'] ?? ''));
        if ($medicationNotes !== '') {
            $updateData['medication_notes'] = $medicationNotes;
        }
        $boarding->update($updateData);

        if ($medicationNotes !== '') {
            MedicationInstruction::updateOrCreate(
                ['pet_id' => $boarding->pet_id],
                ['instructions' => $medicationNotes]
            );
        } else {
            MedicationInstruction::where('pet_id', $boarding->pet_id)->delete();
        }
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

        // Note: We don't delete feeding schedules and medication instructions
        // as they might be needed for other purposes or future boardings
        // Only delete if explicitly needed:
        // FeedingSchedule::where('pet_id', $boarding->pet_id)->delete();
        // MedicationInstruction::where('pet_id', $boarding->pet_id)->delete();

        $boarding->delete();

        return redirect()->route('admin.boarding.index')->with('success', 'Boarding deleted successfully.');
    }
}
