<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\GroomingAppointment;
use App\Models\GroomingService;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GroomingController extends BaseController
{
    /**
     * Display a listing of grooming appointments.
     */
    public function index()
    {
        $groomingAppointments = GroomingAppointment::with([
            'appointment.pet.owner.user',
            'service',
            'groomer'
        ])
            ->orderByDesc('created_at')
            ->get();

        $todayAppointments = GroomingAppointment::whereHas('appointment', function($q) {
            $q->where('type', 'grooming')
              ->whereDate('appointment_date', Carbon::today());
        })->count();

        $completedAppointments = GroomingAppointment::where('status', 'completed')->count();
        $servicesCount = GroomingService::count();

        return view('admin.grooming.index', compact('groomingAppointments', 'todayAppointments', 'completedAppointments', 'servicesCount'));
    }

    /**
     * Show the form for creating a new grooming appointment.
     */
    public function create()
    {
        $pets = Pet::with(['owner.user'])
            ->orderBy('name')
            ->get();

        $services = GroomingService::orderBy('service_name')->get();
        $groomers = User::where('role', 'groomer')->orderBy('first_name')->get();

        return view('admin.grooming.create', compact('pets', 'services', 'groomers'));
    }

    /**
     * Store a newly created grooming appointment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:grooming_services,id',
            'groomer_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create([
            'pet_id' => $data['pet_id'],
            'veterinarian_id' => null,
            'appointment_date' => $data['appointment_date'],
            'status' => 'confirmed',
            'type' => 'grooming',
            'notes' => $data['notes'] ?? null,
        ]);

        GroomingAppointment::create([
            'appointment_id' => $appointment->id,
            'service_id' => $data['service_id'],
            'groomer_id' => $data['groomer_id'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.grooming.index')
            ->with('success', 'Grooming appointment created successfully.');
    }

    /**
     * Display the specified grooming appointment.
     */
    public function show($id)
    {
        $groomingAppointment = GroomingAppointment::with([
            'appointment.pet.owner.user',
            'service',
            'groomer'
        ])->findOrFail($id);

        return view('admin.grooming.show', compact('groomingAppointment'));
    }

    /**
     * Show the form for editing the specified grooming appointment.
     */
    public function edit($id)
    {
        $groomingAppointment = GroomingAppointment::with([
            'appointment.pet.owner.user',
            'service',
            'groomer'
        ])->findOrFail($id);

        $services = GroomingService::orderBy('service_name')->get();
        $groomers = User::where('role', 'groomer')->orderBy('first_name')->get();

        return view('admin.grooming.edit', compact('groomingAppointment', 'services', 'groomers'));
    }

    /**
     * Update the specified grooming appointment.
     */
    public function update(Request $request, $id)
    {
        $groomingAppointment = GroomingAppointment::findOrFail($id);
        $appointment = $groomingAppointment->appointment;

        $data = $request->validate([
            'service_id' => 'required|exists:grooming_services,id',
            'groomer_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $appointment->update([
            'appointment_date' => $data['appointment_date'],
            'status' => $this->mapAppointmentStatus($data['status']),
            'notes' => $data['notes'] ?? null,
        ]);

        $groomingAppointment->update([
            'service_id' => $data['service_id'],
            'groomer_id' => $data['groomer_id'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.grooming.show', $groomingAppointment->id)
            ->with('success', 'Grooming appointment updated successfully.');
    }

    /**
     * Remove the specified grooming appointment.
     */
    public function destroy($id)
    {
        $groomingAppointment = GroomingAppointment::findOrFail($id);
        $appointment = $groomingAppointment->appointment;
        
        $groomingAppointment->delete();
        $appointment->delete();

        return redirect()->route('admin.grooming.index')
            ->with('success', 'Grooming appointment deleted successfully.');
    }

    private function mapAppointmentStatus(string $groomingStatus): string
    {
        return match ($groomingStatus) {
            'scheduled' => 'confirmed',
            'in_progress' => 'in_progress',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => 'pending',
        };
    }

    /**
     * Display a listing of grooming services.
     */
    public function servicesIndex()
    {
        $services = GroomingService::orderBy('service_name')->get();
        $totalServices = $services->count();
        
        return view('admin.grooming.services.index', compact('services', 'totalServices'));
    }

    /**
     * Show the form for creating a new grooming service.
     */
    public function servicesCreate()
    {
        return view('admin.grooming.services.create');
    }

    /**
     * Store a newly created grooming service.
     */
    public function servicesStore(Request $request)
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        GroomingService::create($data);

        return redirect()->route('admin.grooming-services.index')
            ->with('success', 'Grooming service created successfully.');
    }

    /**
     * Display the specified grooming service.
     */
    public function servicesShow($id)
    {
        $service = GroomingService::findOrFail($id);
        
        return view('admin.grooming.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified grooming service.
     */
    public function servicesEdit($id)
    {
        $service = GroomingService::findOrFail($id);
        
        return view('admin.grooming.services.edit', compact('service'));
    }

    /**
     * Update the specified grooming service.
     */
    public function servicesUpdate(Request $request, $id)
    {
        $service = GroomingService::findOrFail($id);

        $data = $request->validate([
            'service_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $service->update($data);

        return redirect()->route('admin.grooming-services.show', $service->id)
            ->with('success', 'Grooming service updated successfully.');
    }

    /**
     * Remove the specified grooming service.
     */
    public function servicesDestroy($id)
    {
        $service = GroomingService::findOrFail($id);
        
        if ($service->groomingAppointments()->count() > 0) {
            return redirect()->route('admin.grooming-services.index')
                ->with('error', 'Cannot delete service that has appointments.');
        }
        
        $service->delete();

        return redirect()->route('admin.grooming-services.index')
            ->with('success', 'Grooming service deleted successfully.');
    }
}
