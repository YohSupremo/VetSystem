<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
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

        $veterinarianId = request()->query('veterinarian_id');

        if ($veterinarianId === '' || $veterinarianId === null) {
            $veterinarianId = null;
        } else {
            $veterinarianId = (int) $veterinarianId;
        }

        $queue = $this->appointmentQueueService->getTodaysQueue($veterinarianId);

        $appointments = [
            'scheduled'   => $queue->where('status', 'scheduled')->sortBy('queue_number'),
            'in_progress' => $queue->where('status', 'in_progress'),
            'completed'   => $queue->where('status', 'completed')->sortByDesc('end_service_time'),
        ];

        $veterinarians = DB::table('users')
            ->where('role', 'veterinarian')
            ->orderBy('first_name')
            ->get(['id', DB::raw("CONCAT(first_name, ' ', last_name) as name")
            ]);

        $stats = $this->appointmentQueueService->getQueueStats();

        return view('admin.queue.index', [
            'appointments'    => $appointments,
            'veterinarians'   => $veterinarians,
            'today'           => $today,
            'stats'           => $stats,
        ]);
    }

    /**
     * Provide JSON data for the queue dashboard (for AJAX polling)
     */
    public function getQueueData(Request $request)
    {
        try {
            $veterinarianId = $request->query('veterinarian_id');

            if ($veterinarianId === 'all' || $veterinarianId === '' || $veterinarianId === null) {
                $veterinarianId = null;
            } else {
                $veterinarianId = (int) $veterinarianId;
            }

            $queue = $this->appointmentQueueService->getTodaysQueue($veterinarianId);

            $data = $queue->map(function ($appointment) {
                return [
                    'id'            => $appointment->id,
                    'queue_number'  => $appointment->queue_number,
                    'pet_name'      => optional($appointment->pet)->name,
                    'type'          => ucfirst($appointment->type),
                    'status'        => $appointment->status,
                    'check_in_time' => optional($appointment->check_in_time)?->format('h:i A'),
                    'wait_time'     => $appointment->check_in_time
                        ? now()->diffInMinutes($appointment->check_in_time)
                        : 0,
                ];
            })->values();

            $stats = $this->appointmentQueueService->getQueueStats();

            return response()->json([
                'success' => true,
                'data'    => $data,
                'stats'   => $stats,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load queue data',
                'error'   => $e->getMessage(),
            ], 500);
        }
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

            // If the client expects JSON (e.g. AJAX), keep JSON response.
            if ($request->expectsJson()) {
                return response()->json([
                    'success'     => true,
                    'message'     => 'Appointment status updated successfully',
                    'appointment' => $appointment->load(['pet', 'veterinarian']),
                ]);
            }

            // Default: redirect back in non-JS flow
            return redirect()
                ->back()
                ->with('success', 'Appointment status updated successfully.');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update appointment status',
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to update appointment status: ' . $e->getMessage());
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
    public function callNext($veterinarianId = null)
    {
        if ($veterinarianId === 'all' || $veterinarianId === '' || $veterinarianId === null) {
            $veterinarianId = null;
        }

        DB::beginTransaction();

        try {
            $query = Appointment::whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->orderBy('queue_number');

            if ($veterinarianId) {
                $query->where('veterinarian_id', $veterinarianId);
            }

            $next = $query->first();

            if (!$next) {
                // For JSON clients
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No more appointments in the queue',
                    ], 404);
                }

                return redirect()
                    ->back()
                    ->with('warning', 'No more appointments in the queue.');
            }

            $inProgressQuery = Appointment::where('status', 'in_progress');

            if ($veterinarianId) {
                $inProgressQuery->where('veterinarian_id', $veterinarianId);
            }

            $inProgressQuery->update([
                'status' => 'completed',
                'end_service_time' => now(),
            ]);

            $next->update([
                'status' => 'in_progress',
                'start_service_time' => now(),
            ]);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Next patient called',
                    'appointment' => $next->load('pet'),
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Next patient called.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to call next patient',
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to call next patient: ' . $e->getMessage());
        }
    }

    /**
     * Show form to create a new queued appointment
     */
    public function create()
    {
        $pets = Pet::with(['owner.user'])->orderBy('name')->get();
        $veterinarians = User::where('role', 'veterinarian')
            ->orderBy('first_name')
            ->get();
        $today = today()->toDateString();

        return view('admin.queue.create', compact('pets', 'veterinarians', 'today'));
    }

    /**
     * Store a new queued appointment (Create)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id'          => 'required|exists:pets,id',
            'veterinarian_id' => 'required|exists:users,id',
            'appointment_date'=> 'required|date',
            'start_time'      => 'required|date_format:H:i',
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'type'            => 'required|in:checkup,vaccination,surgery,dental,grooming,other',
            'reason'          => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        $result = $this->appointmentQueueService->addToQueue($data);

        if (! $result['success']) {
            return back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()
            ->route('admin.queue.index')
            ->with('success', 'Appointment added to queue.');
    }

    /**
     * Show a single queued appointment (Read)
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['pet.owner.user', 'veterinarian']);

        return view('admin.queue.show', compact('appointment'));
    }

    /**
     * Edit page for a queued appointment (Update form)
     */
    public function edit(Appointment $appointment)
    {
        $appointment->load(['pet.owner.user', 'veterinarian']);

        return view('admin.queue.edit', compact('appointment'));
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