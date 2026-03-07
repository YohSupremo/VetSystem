<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
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
        
        // If the user is a veterinarian, only show their assigned appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            $query->where('veterinarian_id', $user->id);
        }
        
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
        
        // Check if user is veterinarian
        $user = auth()->user();
        $isVeterinarian = $user && $user->isVeterinarian();
        
        // Get scheduled staff for current day/time
        $scheduledStaffIds = StaffSchedule::getCurrentScheduledStaffIds();
        
        if ($isVeterinarian) {
            // For vets, only include themselves
            $assignableStaff = collect([$user]);
        } else {
            $assignableStaff = User::whereIn('role', ['veterinarian', 'groomer', 'boarding', 'staff'])
                ->where('is_active', 1)
                ->whereIn('id', $scheduledStaffIds)
                ->orderBy('first_name')
                ->get();
        }
        
        $types = ['consultation', 'vaccination', 'surgery', 'grooming', 'boarding', 'follow_up', 'emergency', 'other'];
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        
        return view('admin.appointments.create', compact('pets', 'assignableStaff', 'types', 'statuses', 'isVeterinarian'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        // Check if user is veterinarian and force their ID
        $user = auth()->user();
        $isVeterinarian = $user && $user->isVeterinarian();
        
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i',
            'type' => 'required|in:consultation,vaccination,surgery,grooming,boarding,follow_up,emergency,other',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'queue_priority' => 'nullable|integer|min:0',
        ]);

        // If veterinarian is creating appointment, automatically assign to them
        if ($isVeterinarian) {
            $validated['veterinarian_id'] = $user->id;
        }

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
        // If the user is a veterinarian, ensure they can only view their assigned appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian() && $appointment->veterinarian_id !== $user->id) {
            abort(403, 'Unauthorized to view this appointment.');
        }
        
        $appointment->load(['pet.owner.user', 'veterinarian']);
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        // If the user is a veterinarian, ensure they can only edit their assigned appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian() && $appointment->veterinarian_id !== $user->id) {
            abort(403, 'Unauthorized to edit this appointment.');
        }
        
        $isVeterinarian = $user && $user->isVeterinarian();
        
        $pets = Pet::with('owner.user')->orderBy('name')->get();
        
        // Get scheduled staff for current day/time
        $scheduledStaffIds = StaffSchedule::getCurrentScheduledStaffIds();
        
        if ($isVeterinarian) {
            // For vets, only include themselves
            $assignableStaff = collect([$user]);
        } else {
            $assignableStaff = User::whereIn('role', ['veterinarian', 'groomer', 'boarding', 'staff'])
                ->where('is_active', 1)
                ->whereIn('id', $scheduledStaffIds)
                ->orderBy('first_name')
                ->get();
        }
        
        $types = ['consultation', 'vaccination', 'surgery', 'grooming', 'boarding', 'follow_up', 'emergency', 'other'];
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        
        return view('admin.appointments.edit', compact('appointment', 'pets', 'assignableStaff', 'types', 'statuses', 'isVeterinarian'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment, NotificationService $notificationService)
    {
        // If the user is a veterinarian, ensure they can only update their assigned appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian() && $appointment->veterinarian_id !== $user->id) {
            abort(403, 'Unauthorized to update this appointment.');
        }
        
        $isVeterinarian = $user && $user->isVeterinarian();
        
        // If appointment is currently cancelled, only allow status to be updated
        if ($appointment->status === 'cancelled') {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            ]);
            
            // Only update status if it changed
            if ($validated['status'] !== $appointment->status) {
                $oldStatus = $appointment->status;
                $appointment->update(['status' => $validated['status']]);
                
                // Send notification to customer about status change
                $appointment->loadMissing('pet.owner.user');
                $customer = $appointment->pet?->owner?->user;

                if ($customer) {
                    $staffName = $user ? trim($user->first_name . ' ' . $user->last_name) : 'Clinic Staff';
                    $staffRole = $user ? ucfirst($user->role) : '';
                    $staffInfo = $staffRole ? $staffName . ' (' . $staffRole . ')' : $staffName;
                    $notificationService->send(
                        $customer,
                        Notification::TYPE_APPOINTMENT,
                        'Appointment Status Updated',
                        'Your appointment status changed from ' . ucfirst(str_replace('_', ' ', (string) $oldStatus)) . ' to ' . ucfirst(str_replace('_', ' ', (string) $appointment->status)) . ' by ' . $staffInfo . '.',
                        [
                            'reference_type' => 'appointment',
                            'reference_id' => $appointment->id,
                            'action_url' => route('customer.appointments.show', $appointment->id),
                        ]
                    );
                }
            }
            
            return redirect()->route('admin.appointments.show', $appointment)
                ->with('success', 'Appointment status updated successfully.');
        }
        
        // Normal validation for non-cancelled appointments
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i',
            'type' => 'required|in:consultation,vaccination,surgery,grooming,boarding,follow_up,emergency,other',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'queue_priority' => 'nullable|integer|min:0',
        ]);

        // If veterinarian is updating, ensure they can't change the assigned staff
        if ($isVeterinarian) {
            $validated['veterinarian_id'] = $user->id;
        }

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

        $oldStatus = $appointment->status;
        $oldAssigneeId = $appointment->veterinarian_id;
        $appointment->update($validated);

        if (!empty($appointment->veterinarian_id) && (int) $appointment->veterinarian_id !== (int) $oldAssigneeId) {
            $assignee = User::find($appointment->veterinarian_id);

            if ($assignee) {
                $assigneeActionUrl = match ($assignee->role) {
                    'groomer' => route('admin.grooming.index'),
                    'boarding' => route('admin.boarding.index'),
                    default => route('veterinarian.appointments.show', $appointment->id),
                };

                $notificationService->send(
                    $assignee,
                    Notification::TYPE_APPOINTMENT,
                    'Appointment Assigned',
                    'An appointment has been assigned to you.',
                    [
                        'reference_type' => 'appointment',
                        'reference_id' => $appointment->id,
                        'action_url' => $assigneeActionUrl,
                    ]
                );
            }
        }

        if ($oldStatus !== $appointment->status) {
            $appointment->loadMissing('pet.owner.user');
            $customer = $appointment->pet?->owner?->user;

            if ($customer) {
                $staffName = $user ? trim($user->first_name . ' ' . $user->last_name) : 'Clinic Staff';
                $staffRole = $user ? ucfirst($user->role) : '';
                $staffInfo = $staffRole ? $staffName . ' (' . $staffRole . ')' : $staffName;
                $notificationService->send(
                    $customer,
                    Notification::TYPE_APPOINTMENT,
                    'Appointment Status Updated',
                    'Your appointment status changed from ' . ucfirst(str_replace('_', ' ', (string) $oldStatus)) . ' to ' . ucfirst(str_replace('_', ' ', (string) $appointment->status)) . ' by ' . $staffInfo . '.',
                    [
                        'reference_type' => 'appointment',
                        'reference_id' => $appointment->id,
                        'action_url' => route('customer.appointments.show', $appointment->id),
                    ]
                );
            }
        }

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully!');
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy(Appointment $appointment)
    {
        // If the user is a veterinarian, prevent them from deleting appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            abort(403, 'Veterinarians are not authorized to delete appointments.');
        }
        
        $appointment->delete();
        
        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully!');
    }

    /**
     * Cancel an appointment (changes status to cancelled).
     */
    public function cancel(Appointment $appointment, NotificationService $notificationService)
    {
        // If the user is a veterinarian, ensure they can only cancel their assigned appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian() && $appointment->veterinarian_id !== $user->id) {
            abort(403, 'Unauthorized to cancel this appointment.');
        }
        
        $appointment->update(['status' => 'cancelled']);

        $appointment->loadMissing('pet.owner.user');
        $customer = $appointment->pet?->owner?->user;
        if ($customer) {
            $staffName = $user ? trim($user->first_name . ' ' . $user->last_name) : 'Clinic Staff';
            $staffRole = $user ? ucfirst($user->role) : '';
            $staffInfo = $staffRole ? $staffName . ' (' . $staffRole . ')' : $staffName;
            $notificationService->send(
                $customer,
                Notification::TYPE_APPOINTMENT,
                'Appointment Cancelled',
                'Your appointment has been cancelled by ' . $staffInfo . '.',
                [
                    'reference_type' => 'appointment',
                    'reference_id' => $appointment->id,
                    'action_url' => route('customer.appointments.show', $appointment->id),
                ]
            );
        }
        
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

    /**
     * Assign an appointment to a veterinarian.
     */
    public function assign(Request $request, Appointment $appointment)
    {
        // Veterinarians cannot reassign appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('warning', 'You are not authorized to assign appointments.');
        }
        
        $validated = $request->validate([
            'veterinarian_id' => 'required|exists:users,id',
        ]);

        $assignee = User::find($validated['veterinarian_id']);
        $requiredRole = $this->requiredAssigneeRoleForType($appointment->type);

        if ($requiredRole !== null && (!$assignee || $assignee->role !== $requiredRole)) {
            return back()->withInput()->withErrors([
                'veterinarian_id' => 'Selected staff must have role: ' . ucfirst($requiredRole) . '.',
            ]);
        }

        $oldVeterinarianId = $appointment->veterinarian_id;
        $appointment->veterinarian_id = $validated['veterinarian_id'];
        $appointment->save();

        // Send notification to the assigned veterinarian
        if ($appointment->veterinarian_id && $appointment->veterinarian_id !== $oldVeterinarianId) {
            $notificationService = app(NotificationService::class);
            $assignee = User::find($appointment->veterinarian_id);
            if ($assignee) {
                $assigneeActionUrl = match ($assignee->role) {
                    'groomer' => route('admin.grooming.index'),
                    'boarding' => route('admin.boarding.index'),
                    default => route('veterinarian.appointments.show', $appointment->id),
                };

                $notificationService->send(
                    $assignee,
                    \App\Models\Notification::TYPE_APPOINTMENT,
                    'Appointment Assigned',
                    'An appointment has been assigned to you.',
                    [
                        'reference_type' => 'appointment',
                        'reference_id' => $appointment->id,
                        'action_url' => $assigneeActionUrl,
                    ]
                );
            }
        }

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment assigned successfully!');
    }

    private function normalizeVaccinationUnacceptedStatuses(): void
    {
        Appointment::query()
            ->where('type', 'vaccination')
            ->where('status', 'confirmed')
            ->update(['status' => 'pending']);
    }
}
