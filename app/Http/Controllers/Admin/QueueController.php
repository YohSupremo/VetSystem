<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\QueueManagementService;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    protected $queueService;

    public function __construct(QueueManagementService $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * Display the queue management dashboard
     */
    public function index()
    {
        $queueStatus = $this->queueService->getQueueStatus();
        return view('admin.queue.index', [
            'waiting' => $queueStatus['waiting'],
            'inProgress' => $queueStatus['in_progress'],
            'completed' => $queueStatus['completed'],
            'nextQueueNumber' => $queueStatus['next_queue_number'],
            'estimatedWaitTime' => $this->queueService->getEstimatedWaitTime(),
        ]);
    }

    /**
     * Add appointment to queue
     */
    public function addToQueue($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->queueService->addToQueue($appointment);
        
        return redirect()->back()->with('success', 'Appointment added to queue');
    }

    /**
     * Start serving an appointment
     */
    public function startServing($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->queueService->startServing($appointment);
        
        return redirect()->back()->with('success', 'Now serving appointment #' . $appointment->queue_number);
    }

    /**
     * Complete an appointment
     */
    public function complete($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->queueService->completeAppointment($appointment);
        
        return redirect()->back()->with('success', 'Appointment completed successfully');
    }

    /**
     * Get queue status via AJAX
     */
    public function getQueueStatus(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $queueStatus = $this->queueService->getQueueStatus($date);
        
        return response()->json([
            'waiting' => $queueStatus['waiting'],
            'in_progress' => $queueStatus['in_progress'],
            'completed' => $queueStatus['completed'],
            'next_queue_number' => $queueStatus['next_queue_number'],
            'estimated_wait_time' => $this->queueService->getEstimatedWaitTime($date),
        ]);
    }
}
