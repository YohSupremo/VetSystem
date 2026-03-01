<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Services\AppointmentQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QueueManagementController extends Controller
{
    protected AppointmentQueueService $queueService;

    public function __construct(AppointmentQueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * Display the queue management dashboard.
     */
    public function index(Request $request)
    {
        $veterinarianId = $request->query('veterinarian_id');
        
        // If the user is a veterinarian, force filter to their own appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            $veterinarianId = $user->id;
        } elseif ($veterinarianId === '' || $veterinarianId === 'all') {
            $veterinarianId = null;
        }

        $queue = $this->queueService->getTodaysQueue($veterinarianId);
        
        // Group appointments by status
        $appointments = [
            'waiting' => $queue->whereIn('status', ['pending', 'confirmed']),
            'in_progress' => $queue->where('status', 'in_progress'),
            'completed' => $queue->where('status', 'completed'),
        ];

        $veterinarians = User::where('role', 'veterinarian')
            ->where('is_active', 1)
            ->orderBy('first_name')
            ->get()
            ->map(function ($vet) {
                $vet->name = 'Dr. ' . trim($vet->first_name . ' ' . $vet->last_name);
                return $vet;
            });

        $stats = $this->queueService->getQueueStats();

        return view('admin.queue.index', compact('appointments', 'veterinarians', 'stats'));
    }

    /**
     * Call the next patient in the queue.
     */
    public function callNext(Request $request)
    {
        $veterinarianId = $request->query('veterinarian_id');
        
        // If the user is a veterinarian, force filter to their own appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            $veterinarianId = $user->id;
        } elseif ($veterinarianId === '' || $veterinarianId === 'all') {
            $veterinarianId = null;
        }

        $result = $this->queueService->callNext($veterinarianId);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('warning', $result['message']);
    }

    /**
     * Complete the current appointment.
     */
    public function completeCurrent(Request $request)
    {
        $veterinarianId = $request->query('veterinarian_id');
        
        // If the user is a veterinarian, force filter to their own appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            $veterinarianId = $user->id;
        } elseif ($veterinarianId === '' || $veterinarianId === 'all') {
            $veterinarianId = null;
        }

        $result = $this->queueService->completeCurrent($veterinarianId);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('warning', $result['message']);
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, $id)
    {
        // If the user is a veterinarian, verify the appointment is assigned to them
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            $appointment = Appointment::findOrFail($id);
            if ($appointment->veterinarian_id !== $user->id) {
                abort(403, 'Unauthorized to update this appointment.');
            }
        }
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
        ]);

        try {
            $appointment = $this->queueService->updateAppointmentStatus(
                $id,
                $request->status
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully',
                    'appointment' => $appointment->load(['pet', 'veterinarian'])
                ]);
            }

            return redirect()->back()->with('success', 'Status updated successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Get queue data (for AJAX polling).
     */
    public function getQueueData(Request $request)
    {
        $veterinarianId = $request->query('veterinarian_id');
        
        // If the user is a veterinarian, force filter to their own appointments
        $user = auth()->user();
        if ($user && $user->isVeterinarian()) {
            $veterinarianId = $user->id;
        } elseif ($veterinarianId === '' || $veterinarianId === 'all') {
            $veterinarianId = null;
        }

        $queue = $this->queueService->getTodaysQueue($veterinarianId);
        
        $data = $queue->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'pet_name' => $appointment->pet->name ?? 'Unknown',
                'pet_species' => $appointment->pet->species ?? 'N/A',
                'owner_name' => $appointment->pet && $appointment->pet->owner && $appointment->pet->owner->user
                    ? trim($appointment->pet->owner->user->first_name . ' ' . $appointment->pet->owner->user->last_name)
                    : 'N/A',
                'type' => ucfirst(str_replace('_', ' ', $appointment->type)),
                'status' => $appointment->status,
                'queue_priority' => $appointment->queue_priority ?? 0,
                'arrival_time' => $appointment->arrival_time ? $appointment->arrival_time->format('h:i A') : null,
                'wait_time' => $appointment->arrival_time 
                    ? now()->diffInMinutes($appointment->arrival_time) 
                    : 0,
            ];
        });

        $stats = $this->queueService->getQueueStats();

        return response()->json([
            'success' => true,
            'data' => $data,
            'stats' => $stats,
        ]);
    }
}