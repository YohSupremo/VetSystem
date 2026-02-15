<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Surgery;
use App\Models\User;
use App\Models\MedicalRecord;
use Illuminate\Pagination\LengthAwarePaginator;

class SurgeryController extends BaseController
{
    /**
     * Display a listing of surgeries.
     */
    public function index()
    {
        $pets = Pet::with([
            'owner',
            'surgeries',
            'appointments' => function ($query) {
                $query->where('type', 'surgery');
            },
        ])
            ->where(function ($query) {
                $query->has('surgeries')
                    ->orWhereHas('appointments', function ($appointmentQuery) {
                        $appointmentQuery->where('type', 'surgery');
                    });
            })
            ->paginate(10);

        $pets->getCollection()->transform(function ($pet) {
            $appointmentCount = $pet->appointments ? $pet->appointments->count() : 0;
            $pet->surgery_appointment_count = $appointmentCount;
            $pet->surgery_total_count = $pet->surgeries->count() + $appointmentCount;
            return $pet;
        });

        return view('admin.surgeries.index', compact('pets'));
    }

    /**
     * Show the form for creating a new surgery.
     */
    public function create()
    {
        $pets = Pet::with('owner.user')->get();
        $surgeons = User::where('role', 'veterinarian')->get();
        $medicalRecords = MedicalRecord::with('pet')->get();

        return view('admin.surgeries.create', compact('pets', 'surgeons', 'medicalRecords'));
    }

    /**
     * Store a newly created surgery in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'surgeon_id' => 'required|exists:users,id',
            'procedure_name' => 'required|string|max:255',
            'scheduled_date' => 'required|date_format:Y-m-d\TH:i',
            'anesthesia_type' => 'nullable|string|max:255',
            'pre_op_notes' => 'nullable|string',
            'post_op_instructions' => 'nullable|string',
            'medical_record_id' => 'nullable|exists:medical_records,id',
        ]);

        Surgery::create([
            'pet_id' => $validated['pet_id'],
            'surgeon_id' => $validated['surgeon_id'],
            'procedure_name' => $validated['procedure_name'],
            'scheduled_date' => $validated['scheduled_date'],
            'anesthesia_type' => $validated['anesthesia_type'],
            'pre_op_notes' => $validated['pre_op_notes'],
            'post_op_instructions' => $validated['post_op_instructions'],
            'medical_record_id' => $validated['medical_record_id'],
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.surgeries.index')
            ->with('success', 'Surgery scheduled successfully!');
    }

    /**
     * Display the specified surgery.
     */
    public function show($id)
    {
        $surgery = Surgery::with(['pet.owner.user', 'surgeon', 'medicalRecord'])
            ->findOrFail($id);

        return view('admin.surgeries.show', compact('surgery'));
    }

    /**
     * Show the form for editing the specified surgery.
     */
    public function edit($id)
    {
        $surgery = Surgery::findOrFail($id);
        $pets = Pet::with('owner.user')->get();
        $surgeons = User::where('role', 'veterinarian')->get();

        return view('admin.surgeries.edit', compact('surgery', 'pets', 'surgeons'));
    }

    /**
     * Update the specified surgery in storage.
     */
    public function update(Request $request, $id)
    {
        $surgery = Surgery::findOrFail($id);

        $validated = $request->validate([
            'surgeon_id' => 'required|exists:users,id',
            'procedure_name' => 'required|string|max:255',
            'scheduled_date' => 'required|date_format:Y-m-d\TH:i',
            'anesthesia_type' => 'nullable|string|max:255',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
            'pre_op_notes' => 'nullable|string',
            'surgery_notes' => 'nullable|string',
            'post_op_instructions' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $surgery->update($validated);

        return redirect()->route('admin.surgeries.show', $surgery->id)
            ->with('success', 'Surgery updated successfully!');
    }

    /**
     * Remove the specified surgery from storage.
     */
    public function destroy($id)
    {
        $surgery = Surgery::findOrFail($id);
        $surgery->delete();

        return redirect()->route('admin.surgeries.index')
            ->with('success', 'Surgery deleted successfully!');
    }

    /**
     * Display surgeries for a specific pet.
     */
    public function byPet($petId)
    {
        $pet = Pet::with('surgeries.surgeon')->findOrFail($petId);
        $surgeries = $pet->surgeries()
            ->with('surgeon')
            ->orderBy('scheduled_date', 'desc')
            ->get();

        $appointmentSurgeries = $pet->appointments()
            ->where('type', 'surgery')
            ->orderBy('appointment_date', 'desc')
            ->get();

        $virtualSurgeries = $appointmentSurgeries->map(function ($appointment) {
            $surgery = new Surgery();
            $surgery->setRelation('pet', $appointment->pet);
            $surgery->setRelation('appointment', $appointment);
            $surgery->setAttribute('procedure_name', 'Appointment (Surgery)');
            $surgery->setAttribute('scheduled_date', $appointment->appointment_date);
            $surgery->setAttribute('status', $this->mapSurgeryStatusFromAppointment($appointment->status));
            $surgery->setAttribute('is_virtual', true);
            return $surgery;
        });

        $allSurgeries = $surgeries
            ->concat($virtualSurgeries)
            ->sortByDesc(function ($item) {
                $date = $item->scheduled_date ?? optional($item->appointment)->appointment_date;
                return $date ? $date->timestamp : 0;
            })
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pageItems = $allSurgeries->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $surgeries = new LengthAwarePaginator(
            $pageItems,
            $allSurgeries->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.surgeries.pet', compact('pet', 'surgeries'));
    }

    private function mapSurgeryStatusFromAppointment(string $appointmentStatus): string
    {
        return match ($appointmentStatus) {
            'in_progress' => 'in_progress',
            'completed' => 'completed',
            'cancelled', 'no_show' => 'cancelled',
            'pending', 'confirmed' => 'scheduled',
            default => 'scheduled',
        };
    }
}
