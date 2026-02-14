<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentQueueService
{
    /**
     * Get today's queue for a specific veterinarian or all.
     */
    public function getTodaysQueue(?int $veterinarianId = null)
    {
        $query = Appointment::with(['pet.owner.user', 'veterinarian'])
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['pending', 'confirmed', 'in_progress', 'completed'])
            ->orderByRaw("CASE 
                WHEN status = 'in_progress' THEN 1 
                WHEN status = 'confirmed' THEN 2
                WHEN status = 'pending' THEN 3
                ELSE 4 
            END")
            ->orderBy('queue_priority', 'desc')
            ->orderBy('appointment_date');

        if ($veterinarianId) {
            $query->where('veterinarian_id', $veterinarianId);
        }

        return $query->get();
    }

    /**
     * Get queue statistics for today.
     */
    public function getQueueStats(): array
    {
        $today = Carbon::today();
        
        return [
            'total_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'waiting' => Appointment::whereDate('appointment_date', $today)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'in_progress' => Appointment::whereDate('appointment_date', $today)
                ->where('status', 'in_progress')
                ->count(),
            'completed' => Appointment::whereDate('appointment_date', $today)
                ->where('status', 'completed')
                ->count(),
        ];
    }

    /**
     * Call the next appointment in queue.
     */
    public function callNext(?int $veterinarianId = null): array
    {
        $query = Appointment::whereDate('appointment_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('queue_priority', 'desc')
            ->orderBy('appointment_date');

        if ($veterinarianId) {
            $query->where('veterinarian_id', $veterinarianId);
        }

        $nextAppointment = $query->first();

        if (!$nextAppointment) {
            return [
                'success' => false,
                'message' => 'No more appointments in queue',
                'appointment' => null
            ];
        }

        $nextAppointment->update([
            'status' => 'in_progress',
            'queue_status' => 'being_served',
            'arrival_time' => $nextAppointment->arrival_time ?? now(),
        ]);

        return [
            'success' => true,
            'message' => 'Next appointment called',
            'appointment' => $nextAppointment->load('pet', 'veterinarian')
        ];
    }

    /**
     * Complete the current in-progress appointment.
     */
    public function completeCurrent(?int $veterinarianId = null): array
    {
        $query = Appointment::where('status', 'in_progress');
        
        if ($veterinarianId) {
            $query->where('veterinarian_id', $veterinarianId);
        }

        $currentAppointment = $query->first();

        if (!$currentAppointment) {
            return [
                'success' => false,
                'message' => 'No appointment in progress',
                'appointment' => null
            ];
        }

        $currentAppointment->update([
            'status' => 'completed',
            'queue_status' => 'completed',
        ]);

        return [
            'success' => true,
            'message' => 'Appointment marked as completed',
            'appointment' => $currentAppointment->load('pet', 'veterinarian')
        ];
    }

    /**
     * Update appointment status.
     */
    public function updateAppointmentStatus(int $appointmentId, string $status, array $additionalData = []): Appointment
    {
        $appointment = Appointment::findOrFail($appointmentId);
        
        $updateData = ['status' => $status];
        
        // Set queue_status based on status
        if ($status === 'in_progress') {
            $updateData['queue_status'] = 'being_served';
            $updateData['arrival_time'] = $updateData['arrival_time'] ?? now();
        } elseif ($status === 'completed') {
            $updateData['queue_status'] = 'completed';
        } elseif (in_array($status, ['pending', 'confirmed'])) {
            $updateData['queue_status'] = 'waiting';
        }

        $appointment->update(array_merge($updateData, $additionalData));
        
        return $appointment->fresh();
    }
}
