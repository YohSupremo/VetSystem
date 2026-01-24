<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueueManagementController extends Controller
{
    protected AppointmentQueueService $appointmentQueueService;

    public function __construct(AppointmentQueueService $appointmentQueueService)
    {
        $this->appointmentQueueService = $appointmentQueueService;
    }

    /**
     * Display the queue management dashboard
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $queue = $this->appointmentQueueService->getTodaysQueue();

        $appointments = [
            'scheduled'   => $queue->where('status', 'scheduled')->sortBy('queue_number'),
            'in_progress' => $queue->where('status', 'in_progress'),
            'completed'   => $queue->where('status', 'completed')->sortByDesc('end_service_time'),
        ];

        $veterinarians = DB::table('users')
            ->where('role', 'veterinarian')
            ->orderBy('first_name')
            ->get(['id', DB::raw("first_name || ' ' || last_name as name")
            ]);

        return view('admin.queue.index', compact(
            'appointments',
            'veterinarians',
            'today'
        ));
    }

    /**
     * Update the appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,no_show',
            'notes'  => 'nullable|string|max:1000',
        ]);

        try {
            $appointment = $this->appointmentQueueService
                ->updateAppointmentStatus($id, $request->status, $request->only('notes'));

            if ($request->status === 'in_progress') {
                $appointment->update(['start_service_time' => now()]);
            } elseif ($request->status === 'completed') {
                $appointment->update(['end_service_time' => now()]);
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Appointment status updated successfully',
                'appointment' => $appointment->load(['pet', 'veterinarian']),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment status',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the current queue for a specific veterinarian
     */
    public function getVeterinarianQueue($veterinarianId)
    {
        try {
            $queue = $this->appointmentQueueService->getTodaysQueue($veterinarianId);

            return response()->json([
                'success' => true,
                'data' => $queue->map(fn ($appointment) => [
                    'id'           => $appointment->id,
                    'queue_number' => $appointment->queue_number,
                    'pet_name'     => $appointment->pet->name,
                    'type'         => ucfirst($appointment->type),
                    'status'       => $appointment->status,
                    'check_in_time'=> optional($appointment->check_in_time)?->format('h:i A'),
                    'wait_time'    => $appointment->check_in_time
                        ? now()->diffInMinutes($appointment->check_in_time)
                        : 0,
                ]),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load queue',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Call the next patient in the queue
     */
    public function callNext($veterinarianId)
    {
        DB::beginTransaction();

        try {
            $next = Appointment::where('veterinarian_id', $veterinarianId)
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->orderBy('queue_number')
                ->first();

            if (!$next) {
                return response()->json([
                    'success' => false,
                    'message' => 'No more appointments in the queue',
                ], 404);
            }

            Appointment::where('veterinarian_id', $veterinarianId)
                ->where('status', 'in_progress')
                ->update([
                    'status' => 'completed',
                    'end_service_time' => now(),
                ]);

            $next->update([
                'status' => 'in_progress',
                'start_service_time' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Next patient called',
                'appointment' => $next->load('pet'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to call next patient',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Queue statistics
     */
    public function getQueueStats()
    {
        $today = today();

        return response()->json([
            'success' => true,
            'data' => [
                'total_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
                'completed'          => Appointment::whereDate('appointment_date', $today)->where('status', 'completed')->count(),
                'in_progress'        => Appointment::whereDate('appointment_date', $today)->where('status', 'in_progress')->count(),
                'waiting'            => Appointment::whereDate('appointment_date', $today)->where('status', 'scheduled')->count(),
            ],
        ]);
    }
}