<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QueueManagementService
{
    /**
     * Get the next available queue number for the given date
     */
    public function getNextQueueNumber($date): int
    {
        $lastQueue = Appointment::whereDate('appointment_date', $date)
            ->max('queue_number');

        return $lastQueue ? $lastQueue + 1 : 1;
    }

    /**
     * Add an appointment to the queue
     */
    public function addToQueue(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $appointment->queue_number = $this->getNextQueueNumber($appointment->appointment_date);
            $appointment->status = 'waiting';
            $appointment->check_in_time = now();
            $appointment->save();

            return $appointment;
        });
    }

    /**
     * Start serving an appointment
     */
    public function startServing(Appointment $appointment): Appointment
    {
        $appointment->status = 'in_progress';
        $appointment->start_service_time = now();
        $appointment->save();

        return $appointment;
    }

    /**
     * Complete an appointment
     */
    public function completeAppointment(Appointment $appointment): Appointment
    {
        $appointment->status = 'completed';
        $appointment->end_service_time = now();
        $appointment->save();

        return $appointment;
    }

    /**
     * Get the current queue status
     */
    public function getQueueStatus($date = null)
    {
        $date = $date ?: now()->toDateString();

        return [
            'waiting' => Appointment::whereDate('appointment_date', $date)
                ->where('status', 'waiting')
                ->orderBy('queue_number')
                ->get(),
            'in_progress' => Appointment::whereDate('appointment_date', $date)
                ->where('status', 'in_progress')
                ->orderBy('start_service_time')
                ->get(),
            'completed' => Appointment::whereDate('appointment_date', $date)
                ->where('status', 'completed')
                ->orderBy('end_service_time', 'desc')
                ->take(10)
                ->get(),
            'next_queue_number' => $this->getNextQueueNumber($date),
        ];
    }

    /**
     * Get the estimated wait time for a new appointment
     */
    public function getEstimatedWaitTime($date = null): int
    {
        $date = $date ?: now()->toDateString();
        
        $avgAppointmentTime = Appointment::whereDate('appointment_date', $date)
            ->where('status', 'completed')
            ->whereNotNull('start_service_time')
            ->whereNotNull('end_service_time')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, start_service_time, end_service_time)) as avg_time'))
            ->value('avg_time') ?: 15; // Default to 15 minutes if no data

        $waitingCount = Appointment::whereDate('appointment_date', $date)
            ->where('status', 'waiting')
            ->count();

        return (int) round($waitingCount * $avgAppointmentTime);
    }
}
