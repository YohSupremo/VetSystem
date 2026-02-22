<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Models\StaffSchedule;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request)
    {
        $this->normalizeVaccinationUnacceptedStatuses();

        $query = Appointment::with(['pet.owner.user', 'veterinarian']);
        
        // Apply filters
        if ($request->filled('pet_id')) {
            $query->where('pet_id', $request->pet_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }
        
        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(20);
        
        // Add formatted properties for the view
        $appointments->getCollection()->transform(function ($appointment) {
            $appointment->formatted_date = $appointment->appointment_date 
                ? $appointment->appointment_date->format('M d, Y g:i A')
                : 'TBD';
            
            $appointment->pet_name = $appointment->pet->name ?? 'Unnamed Pet';
            $appointment->pet_species = $appointment->pet->species ?? 'N/A';
            
            $appointment->owner_name = $appointment->pet && $appointment->pet->owner && $appointment->pet->owner->user
                ? trim($appointment->pet->owner->user->first_name . ' ' . $appointment->pet->owner->user->last_name)
                : 'N/A';
            
            $appointment->veterinarian_name = $appointment->veterinarian
                ? 'Dr. ' . trim($appointment->veterinarian->first_name . ' ' . $appointment->veterinarian->last_name)
                : 'Unassigned';
            
            $appointment->type_label = $appointment->type
                ? ucfirst(str_replace('_', ' ', $appointment->type))
                : 'Unknown';
            
            $appointment->status_label = $appointment->status
                ? ucfirst(str_replace('_', ' ', $appointment->status))
                : 'Unknown';
            
            $appointment->status_badge = match ($appointment->status) {
                'completed' => 'success',
                'cancelled', 'no_show' => 'danger',
                default => 'warning',
            };
            
            return $appointment;
        });
        
        // Get all pets for filter dropdown
        $pets = Pet::with('owner.user')->orderBy('name')->get();
        
        // Available types and statuses for filters
        $types = ['consultation', 'vaccination', 'surgery', 'grooming', 'boarding', 'follow_up', 'emergency', 'other'];
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        
        // Filters for the view
        $filters = [
            'pet_id' => $request->pet_id,
            'status' => $request->status,
            'type' => $request->type,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];
        
        $hasAppointments = true; // Table exists since we're using Eloquent
        
        return view('admin.appointments.index', compact('appointments', 'pets', 'types', 'statuses', 'filters', 'hasAppointments'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $pets = Pet::with('owner.user')->orderBy('name')->get();
        
        // Get scheduled staff for current day/time
        $scheduledStaffIds = StaffSchedule::getCurrentScheduledStaffIds();
        $assignableStaff = User::whereIn('role', ['veterinarian', 'groomer', 'boarding', 'staff'])
            ->where('is_active', 1)
            ->whereIn('id', $scheduledStaffIds)
            ->orderBy('first_name')
            ->get();
        
        $types = ['consultation', 'vaccination', 'surgery', 'grooming', 'boarding', 'follow_up', 'emergency', 'other'];
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        
        return view('admin.appointments.create', compact('pets', 'assignableStaff', 'types', 'statuses'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i',
            'type' => 'required|in:consultation,vaccination,surgery,grooming,boarding,follow_up,emergency,other',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'queue_priority' => 'nullable|integer|min:0',
        ]);

        if (($validated['type'] ?? null) === 'vaccination' && ($validated['status'] ?? null) === 'confirmed') {
            $validated['status'] = 'pending';
        }

        if (!empty($validated['veterinarian_id'])) {
            $assignee = User::find($validated['veterinarian_id']);
            $requiredRole = $this->requiredAssigneeRoleForType($validated['type'] ?? null);

            if ($requiredRole !== null && (!$assignee || $assignee->role !== $requiredRole)) {
                return back()->withInput()->withErrors([
                    'veterinarian_id' => 'Selected staff must have role: ' . ucfirst($requiredRole) . '.',
                ]);
            }
        }

        // Parse the datetime-local format to proper DateTime
        $validated['appointment_date'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['appointment_date']);

        $appointment = Appointment::create($validated);

        $actor = auth()->user();
        if ($actor) {
            $notificationService->send(
                $actor,
                \App\Models\Notification::TYPE_APPOINTMENT,
                'Appointment Created',
                'A new appointment has been scheduled.',
                [
                    'reference_type' => 'appointment',
                    'reference_id' => $appointment->id,
                    'action_url' => route('admin.appointments.show', $appointment->id),
                ]
            );
        }

        if (!empty($appointment->veterinarian_id)) {
            $assignee = User::find($appointment->veterinarian_id);
            if ($assignee) {
                $notificationService->send(
                    $assignee,
                    \App\Models\Notification::TYPE_APPOINTMENT,
                    'New Appointment Assigned',
                    'A new appointment has been assigned to you.',
                    [
                        'reference_type' => 'appointment',
                        'reference_id' => $appointment->id,
                    ]
                );
            }
        }

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment created successfully!');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['pet.owner.user', 'veterinarian']);
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $pets = Pet::with('owner.user')->orderBy('name')->get();
        
        // Get scheduled staff for current day/time
        $scheduledStaffIds = StaffSchedule::getCurrentScheduledStaffIds();
        $assignableStaff = User::whereIn('role', ['veterinarian', 'groomer', 'boarding', 'staff'])
            ->where('is_active', 1)
            ->whereIn('id', $scheduledStaffIds)
            ->orderBy('first_name')
            ->get();
        
        $types = ['consultation', 'vaccination', 'surgery', 'grooming', 'boarding', 'follow_up', 'emergency', 'other'];
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        
        return view('admin.appointments.edit', compact('appointment', 'pets', 'assignableStaff', 'types', 'statuses'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i',
            'type' => 'required|in:consultation,vaccination,surgery,grooming,boarding,follow_up,emergency,other',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'queue_priority' => 'nullable|integer|min:0',
        ]);

        if (($validated['type'] ?? null) === 'vaccination' && ($validated['status'] ?? null) === 'confirmed') {
            $validated['status'] = 'pending';
        }

        if (!empty($validated['veterinarian_id'])) {
            $assignee = User::find($validated['veterinarian_id']);
            $requiredRole = $this->requiredAssigneeRoleForType($validated['type'] ?? null);

            if ($requiredRole !== null && (!$assignee || $assignee->role !== $requiredRole)) {
                return back()->withInput()->withErrors([
                    'veterinarian_id' => 'Selected staff must have role: ' . ucfirst($requiredRole) . '.',
                ]);
            }
        }

        // Parse the datetime-local format to proper DateTime
        $validated['appointment_date'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['appointment_date']);

        $appointment->update($validated);

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully!');
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        
        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully!');
    }

    /**
     * Cancel an appointment (changes status to cancelled).
     */
    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        
        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment has been cancelled successfully!');
    }

    private function requiredAssigneeRoleForType(?string $type): ?string
    {
        return match ($type) {
            'consultation', 'vaccination', 'surgery', 'follow_up', 'emergency' => 'veterinarian',
            'grooming' => 'groomer',
            'boarding' => 'boarding',
            default => null,
        };
    }

    private function normalizeVaccinationUnacceptedStatuses(): void
    {
        Appointment::query()
            ->where('type', 'vaccination')
            ->where('status', 'confirmed')
            ->update(['status' => 'pending']);
    }
}
