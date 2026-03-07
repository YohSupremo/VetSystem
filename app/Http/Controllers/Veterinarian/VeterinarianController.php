<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Vaccination;
use App\Models\LaboratoryTest;
use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;

class VeterinarianController extends Controller
{
    public function dashboard()
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        // Get today's appointments for this veterinarian
        $todayAppointments = Appointment::where('veterinarian_id', $veterinarian->id)
            ->whereDate('appointment_date', now()->toDateString())
            ->with(['pet.owner'])
            ->orderBy('start_time', 'asc')
            ->get();

        // Get upcoming appointments (next 7 days) - only assigned to this veterinarian
        $upcomingAppointments = Appointment::where('veterinarian_id', $veterinarian->id)
            ->whereDate('appointment_date', '>', now()->toDateString())
            ->whereDate('appointment_date', '<=', now()->addDays(7)->toDateString())
            ->with(['pet.owner'])
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Get queue statistics for today
        $queueStats = [
            'waiting' => $todayAppointments->where('status', 'scheduled')->count(),
            'in_progress' => $todayAppointments->where('status', 'in_progress')->count(),
            'completed' => $todayAppointments->where('status', 'completed')->count(),
        ];

        // Get vaccination statistics - only for this veterinarian's assigned appointments
        $vaccinationStats = [
            'total' => Vaccination::where('veterinarian_id', $veterinarian->id)->count(),
            'today' => Vaccination::where('veterinarian_id', $veterinarian->id)
                ->whereDate('vaccination_date', now()->toDateString())->count(),
            'upcoming' => Vaccination::where('veterinarian_id', $veterinarian->id)
                ->whereDate('vaccination_date', '>', now()->toDateString())
                ->whereDate('vaccination_date', '<=', now()->addDays(30)->toDateString())->count(),
        ];

        // Get laboratory statistics - only for this veterinarian's assigned tests
        $labStats = [
            'total' => LaboratoryTest::where('veterinarian_id', $veterinarian->id)->count(),
            'pending' => LaboratoryTest::where('veterinarian_id', $veterinarian->id)
                ->where('status', 'pending')->count(),
            'completed' => LaboratoryTest::where('veterinarian_id', $veterinarian->id)
                ->where('status', 'completed')->count(),
        ];

        // Get recent patients (pets this veterinarian has seen) - only assigned appointments
        $recentPatients = Pet::whereHas('appointments', function ($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })
        ->with('owner')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        return view('veterinarian.dashboard', compact(
            'veterinarian',
            'todayAppointments',
            'upcomingAppointments',
            'queueStats',
            'vaccinationStats',
            'labStats',
            'recentPatients'
        ));
    }

    public function appointments()
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $appointments = Appointment::where('veterinarian_id', $veterinarian->id)
            ->with(['pet', 'pet.owner'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('veterinarian.appointments.index', compact('appointments'));
    }

    public function showAppointment($id)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $appointment = Appointment::where('veterinarian_id', $veterinarian->id)
            ->with(['pet', 'pet.owner', 'pet.medicalRecords'])
            ->findOrFail($id);

        return view('veterinarian.appointments.show', compact('appointment'));
    }

    public function updateAppointmentStatus(Request $request, NotificationService $notificationService, $id)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        // Temporarily simplified query for debugging
        $appointment = Appointment::findOrFail($id);
        $previousStatus = $appointment->status;

        // Debug: Log appointment found
        \Log::info('Appointment found: ' . $appointment->id . ' Status: ' . $appointment->status . ' Vet ID: ' . $veterinarian->id . ' Appt Vet ID: ' . ($appointment->veterinarian_id ?: 'null'));

        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Debug: Log received status
        \Log::info('Received status: ' . $request->status);

        // Update timestamps based on status
        if ($request->status === 'in_progress' && !$appointment->start_service_time) {
            $appointment->start_service_time = now();
        } elseif ($request->status === 'completed' && !$appointment->end_service_time) {
            $appointment->end_service_time = now();
        }

        $appointment->status = $request->status;
        
        if ($request->notes) {
            $appointment->notes = $request->notes;
        }

        $appointment->save();

        if ($previousStatus !== $appointment->status) {
            $appointment->loadMissing('pet.owner.user');
            $customer = $appointment->pet?->owner?->user;

            if ($customer) {
                $vetUser = auth()->user();
                $vetName = $vetUser ? 'Dr. ' . trim($vetUser->first_name . ' ' . $vetUser->last_name) : 'Veterinarian';
                $notificationService->send(
                    $customer,
                    Notification::TYPE_APPOINTMENT,
                    'Appointment Status Updated',
                    'Your appointment status changed from ' . ucfirst(str_replace('_', ' ', (string) $previousStatus)) . ' to ' . ucfirst(str_replace('_', ' ', (string) $appointment->status)) . ' by ' . $vetName . '.',
                    [
                        'reference_type' => 'appointment',
                        'reference_id' => $appointment->id,
                        'action_url' => route('customer.appointments.show', $appointment->id),
                    ]
                );
            }
        }

        // Debug: Log final status
        \Log::info('Final appointment status: ' . $appointment->status);

        return redirect()->back()->with('success', 'Appointment status updated successfully!');
    }

    public function createAppointment()
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        // Get all pets for dropdown
        $pets = Pet::with('owner')->get();
        
        return view('veterinarian.appointments.create', compact('veterinarian', 'pets'));
    }

    public function storeAppointment(Request $request)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        $appointment = Appointment::create([
            'pet_id' => $request->pet_id,
            'veterinarian_id' => $veterinarian->id,
            'appointment_date' => $request->appointment_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'scheduled'
        ]);

        return redirect()->route('veterinarian.appointments.index')
            ->with('success', 'Appointment created successfully!');
    }

    public function patients()
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $patients = Pet::whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })
        ->with('owner')
        ->orderBy('created_at', 'desc')
        ->paginate(12);

        return view('veterinarian.patients.index', compact('patients'));
    }

    public function showPatient($id)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $pet = Pet::whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })
        ->with(['owner', 'medicalRecords', 'appointments', 'vaccinations', 'laboratoryTests'])
        ->findOrFail($id);

        return view('veterinarian.patients.show', compact('pet'));
    }

    public function cancelAppointment(NotificationService $notificationService, $id)
    {
        // Get authenticated veterinarian from session
        $username = session('username');
        $veterinarian = User::where('username', $username)->first();
        
        if (!$veterinarian || !$veterinarian->isVeterinarian()) {
            return redirect()->route('login')->with('error', 'Access denied. Veterinarian access required.');
        }

        $appointment = Appointment::where('veterinarian_id', $veterinarian->id)
            ->findOrFail($id);

        // Only allow cancellation of scheduled appointments
        if ($appointment->status !== 'scheduled') {
            return redirect()->back()->with('error', 'Only scheduled appointments can be cancelled.');
        }

        $appointment->status = 'cancelled';
        $appointment->notes = ($appointment->notes ?? '') . "\n\nCancelled on: " . now()->format('Y-m-d H:i');
        $appointment->save();

        $appointment->loadMissing('pet.owner.user');
        $customer = $appointment->pet?->owner?->user;

        if ($customer) {
            $vetUser = auth()->user();
            $vetName = $vetUser ? 'Dr. ' . trim($vetUser->first_name . ' ' . $vetUser->last_name) : 'Veterinarian';
            $notificationService->send(
                $customer,
                Notification::TYPE_APPOINTMENT,
                'Appointment Cancelled',
                'Your appointment has been cancelled by ' . $vetName . '.',
                [
                    'reference_type' => 'appointment',
                    'reference_id' => $appointment->id,
                    'action_url' => route('customer.appointments.show', $appointment->id),
                ]
            );
        }

        return redirect()->route('veterinarian.appointments.index')
            ->with('success', 'Appointment cancelled successfully!');
    }
}
