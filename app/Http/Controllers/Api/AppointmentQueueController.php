<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentQueueController extends Controller
{
    protected $appointmentQueueService;

    public function __construct(AppointmentQueueService $appointmentQueueService)
    {
        $this->appointmentQueueService = $appointmentQueueService;
        $this->middleware('auth:api');
    }

    /**
     * Get filtered appointments
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['date', 'status', 'veterinarian_id', 'type']);
        $appointments = $this->appointmentQueueService->getFilteredAppointments($filters);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Add a new appointment to the queue
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:checkup,vaccination,surgery,dental,grooming,other',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $appointment = $this->appointmentQueueService->addToQueue($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment added to queue successfully',
                'data' => $appointment
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add appointment to queue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update appointment status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $appointment = $this->appointmentQueueService->updateAppointmentStatus(
                $id,
                $request->status,
                $request->only(['notes'])
            );

            return response()->json([
                'success' => true,
                'message' => 'Appointment status updated successfully',
                'data' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the current queue position for an appointment
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQueuePosition($id)
    {
        try {
            $queueInfo = $this->appointmentQueueService->getQueuePosition($id);
            
            return response()->json([
                'success' => true,
                'data' => $queueInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get queue position',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's queue for a specific veterinarian or all
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTodaysQueue(Request $request)
    {
        $veterinarianId = $request->query('veterinarian_id');
        
        try {
            $queue = $this->appointmentQueueService->getTodaysQueue($veterinarianId);
            
            return response()->json([
                'success' => true,
                'data' => $queue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve today\'s queue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get appointment types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointmentTypes()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'checkup' => 'Regular Checkup',
                'vaccination' => 'Vaccination',
                'surgery' => 'Surgery',
                'dental' => 'Dental Care',
                'grooming' => 'Grooming',
                'other' => 'Other'
            ]
        ]);
    }
}
