<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentQueueService
{

    /**
     * Get appointments with filtering options
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFilteredAppointments(array $filters = [])
    {
        $query = Appointment::with(['pet', 'veterinarian'])
            ->when(isset($filters['date']), function ($q) use ($filters) {
                return $q->whereDate('appointment_date', $filters['date']);
            })
            ->when(isset($filters['status']), function ($q) use ($filters) {
                return $q->where('status', $filters['status']);
            })
            ->when(isset($filters['veterinarian_id']), function ($q) use ($filters) {
                return $q->where('veterinarian_id', $filters['veterinarian_id']);
            })
            ->when(isset($filters['type']), function ($q) use ($filters) {
                return $q->where('type', $filters['type']);
            })
            ->orderBy('appointment_date')
            ->orderBy('start_time');

        return $query->get();
    }

    /**
     * Call the next appointment in queue for a veterinarian
     *
     * @param int|null $veterinarianId
     * @return array
     */
    public function callNext(?int $veterinarianId = null): array
    {
        return DB::transaction(function () use ($veterinarianId) {
            // Find the next scheduled appointment
            $query = Appointment::where('status', 'scheduled')
                ->whereDate('appointment_date', Carbon::today())
                ->orderBy('queue_number');

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

            // Update the appointment status to in_progress
            $nextAppointment->update([
                'status' => 'in_progress',
                'start_service_time' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Next appointment called',
                'appointment' => $nextAppointment->load('pet', 'veterinarian')
            ];
        });
    }

    /**
     * Complete the current in-progress appointment
     *
     * @param int|null $veterinarianId
     * @return array
     */
    public function completeCurrent(?int $veterinarianId = null): array
    {
        return DB::transaction(function () use ($veterinarianId) {
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

            // Update the appointment status to completed
            $currentAppointment->update([
                'status' => 'completed',
                'end_service_time' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Appointment marked as completed',
                'appointment' => $currentAppointment->load('pet', 'veterinarian')
            ];
        });
    }

    /**
     * Add an appointment to the queue
     *
     * @param array $appointmentData
     * @return array
     */
    public function addToQueue(array $appointmentData): array
    {
        return DB::transaction(function () use ($appointmentData) {
            // Validate required fields
            $requiredFields = ['pet_id', 'veterinarian_id', 'appointment_date'];
            foreach ($requiredFields as $field) {
                if (empty($appointmentData[$field])) {
                    throw new \InvalidArgumentException("Missing required field: {$field}");
                }
            }

            // Get the next queue number for the date
            $lastQueueNumber = Appointment::whereDate('appointment_date', $appointmentData['appointment_date'])
                ->max('queue_number') ?? 0;

            try {
                $appointment = new Appointment($appointmentData);
                $appointment->queue_number = $lastQueueNumber + 1;
                $appointment->status = 'scheduled';
                $appointment->save();

                return [
                    'success' => true,
                    'message' => 'Appointment added to queue',
                    'appointment' => $appointment->load('pet', 'veterinarian')
                ];
            } catch (\Exception $e) {
                Log::error('Error adding appointment to queue: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Failed to add appointment to queue: ' . $e->getMessage(),
                    'appointment' => null
                ];
            }
        });
    }

    /**
     * Get queue statistics
     *
     * @return array
     */
    public function getQueueStats(): array
    {
        $today = Carbon::today();
        
        $stats = [
            'total_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'waiting' => Appointment::whereDate('appointment_date', $today)
                ->where('status', 'scheduled')
                ->count(),
            'in_progress' => Appointment::whereDate('appointment_date', $today)
                ->where('status', 'in_progress')
                ->count(),
            'completed' => Appointment::whereDate('appointment_date', $today)
                ->where('status', 'completed')
                ->count(),
            'average_wait_time' => $this->calculateAverageWaitTime($today),
            'veterinarians' => User::where('role', 'veterinarian')
                ->withCount(['appointments' => function($query) use ($today) {
                    $query->whereDate('appointment_date', $today);
                }])
                ->get()
                ->map(function($vet) use ($today) {
                    return [
                        'id' => $vet->id,
                        'name' => $vet->first_name . ' ' . $vet->last_name,
                        'total_appointments' => $vet->appointments_count,
                        'in_progress' => $vet->appointments()
                            ->whereDate('appointment_date', $today)
                            ->where('status', 'in_progress')
                            ->count()
                    ];
                })
        ];

        return $stats;
    }

    /**
     * Calculate average wait time for completed appointments
     *
     * @param Carbon $date
     * @return string
     */
    private function calculateAverageWaitTime(Carbon $date): string
    {
        $appointments = Appointment::whereDate('appointment_date', $date)
            ->where('status', 'completed')
            ->whereNotNull('start_service_time')
            ->whereNotNull('end_service_time')
            ->get();

        if ($appointments->isEmpty()) {
            return 'No data';
        }

        $totalMinutes = $appointments->sum(function($appointment) {
            return $appointment->start_service_time->diffInMinutes($appointment->end_service_time);
        });

        $averageMinutes = $totalMinutes / $appointments->count();
        
        return $averageMinutes > 60 
            ? round($averageMinutes / 60, 1) . ' hours' 
            : round($averageMinutes) . ' minutes';
    }

    /**
     * Update appointment status
     *
     * @param int $appointmentId
     * @param string $status
     * @param array $additionalData
     * @return Appointment
     */
    public function updateAppointmentStatus(int $appointmentId, string $status, array $additionalData = []): Appointment
    {
        $appointment = Appointment::findOrFail($appointmentId);
        
        $updateData = ['status' => $status];
        
        // Set timestamps based on status
        switch ($status) {
            case 'in_progress':
                $updateData['start_service_time'] = now();
                break;
            case 'completed':
                $updateData['end_service_time'] = now();
                break;
            case 'cancelled':
            case 'no_show':
                // Reset queue number if appointment is cancelled or marked as no-show
                $updateData['queue_number'] = null;
                break;
        }

        $appointment->update(array_merge($updateData, $additionalData));
        
        return $appointment->fresh();
    }

    /**
     * Get the current queue position for an appointment
     *
     * @param int $appointmentId
     * @return array
     */
    public function getQueuePosition(int $appointmentId): array
    {
        $appointment = Appointment::findOrFail($appointmentId);
        
        if (!$appointment->queue_number) {
            return [
                'position' => null,
                'total_in_queue' => 0,
                'estimated_wait_time' => 0
            ];
        }

        $queue = Appointment::whereDate('appointment_date', $appointment->appointment_date)
            ->where('status', 'scheduled')
            ->where('queue_number', '<=', $appointment->queue_number)
            ->orderBy('queue_number')
            ->get();

        $position = $queue->search(function ($item) use ($appointment) {
            return $item->id === $appointment->id;
        }) + 1;

        // Calculate estimated wait time (assuming 15 minutes per appointment)
        $estimatedWaitTime = ($position - 1) * 15;

        return [
            'position' => $position,
            'total_in_queue' => $queue->count(),
            'estimated_wait_time' => $estimatedWaitTime
        ];
    }

    /**
     * Get today's queue for a specific veterinarian or all veterinarians if none specified
     *
     * @param int|null $veterinarianId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTodaysQueue(?int $veterinarianId = null)
    {
        $query = Appointment::with(['pet', 'veterinarian'])
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['scheduled', 'in_progress', 'completed'])
            ->orderByRaw("CASE 
                WHEN status = 'in_progress' THEN 1 
                WHEN status = 'scheduled' THEN 2 
                ELSE 3 
            END")
            ->orderBy('queue_number');

        if ($veterinarianId) {
            $query->where('veterinarian_id', $veterinarianId);
        }

        return $query->get();
    }
}
