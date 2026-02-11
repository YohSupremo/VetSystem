<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Pet;
use App\Models\MedicalRecord;
use Carbon\Carbon;

class VeterinarianController extends Controller
{
    public function dashboard()
    {
        // Mock veterinarian data for now
        $veterinarian = new User([
            'id' => 1,
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'specialization' => 'Small Animals'
        ]);
        
        // Get today's appointments
        $todayAppointments = Appointment::with(['pet', 'pet.owner'])
            ->where('veterinarian_id', $veterinarian->id)
            ->whereDate('appointment_date', Carbon::today())
            ->orderBy('start_time')
            ->get();

        // Get upcoming appointments
        $upcomingAppointments = Appointment::with(['pet', 'pet.owner'])
            ->where('veterinarian_id', $veterinarian->id)
            ->where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', Carbon::today())
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Get queue statistics
        $queueStats = [
            'waiting' => Appointment::where('veterinarian_id', $veterinarian->id)
                ->where('status', 'scheduled')
                ->whereDate('appointment_date', Carbon::today())
                ->count(),
            'in_progress' => Appointment::where('veterinarian_id', $veterinarian->id)
                ->where('status', 'in_progress')
                ->whereDate('appointment_date', Carbon::today())
                ->count(),
            'completed' => Appointment::where('veterinarian_id', $veterinarian->id)
                ->where('status', 'completed')
                ->whereDate('appointment_date', Carbon::today())
                ->count(),
        ];

        // Get recent patients
        $recentPatients = Pet::with(['owner', 'appointments' => function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id)
                  ->orderBy('created_at', 'desc')
                  ->limit(1);
        }])
        ->whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })
        ->orderBy('updated_at', 'desc')
        ->take(5)
        ->get();

        return view('veterinarian.dashboard', compact(
            'todayAppointments',
            'upcomingAppointments', 
            'queueStats',
            'recentPatients'
        ));
    }

    public function appointments()
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $appointments = Appointment::with(['pet', 'pet.owner'])
            ->where('veterinarian_id', $veterinarian->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('veterinarian.appointments.index', compact('appointments'));
    }

    public function showAppointment($id)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $appointment = Appointment::with(['pet', 'pet.owner', 'pet.medicalRecords'])
            ->where('veterinarian_id', $veterinarian->id)
            ->findOrFail($id);

        return view('veterinarian.appointments.show', compact('appointment'));
    }

    public function updateAppointmentStatus(Request $request, $id)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $appointment = Appointment::where('veterinarian_id', $veterinarian->id)
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $appointment->status = $request->status;
        
        if ($request->notes) {
            $appointment->notes = $request->notes;
        }

        // Update timestamps based on status
        if ($request->status === 'in_progress' && !$appointment->start_service_time) {
            $appointment->start_service_time = now();
        } elseif ($request->status === 'completed' && !$appointment->end_service_time) {
            $appointment->end_service_time = now();
        }

        $appointment->save();

        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }

    public function patients()
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $patients = Pet::with(['owner', 'appointments' => function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        }])
        ->whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })
        ->orderBy('name')
        ->paginate(10);

        return view('veterinarian.patients.index', compact('patients'));
    }

    public function showPatient($id)
    {
        // Mock veterinarian data
        $veterinarian = new User(['id' => 1]);
        
        $pet = Pet::with([
            'owner',
            'medicalRecords' => function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orderBy('created_at', 'desc');
            },
            'appointments' => function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orderBy('appointment_date', 'desc');
            },
            'vaccinations',
            'prescriptions' => function($query) use ($veterinarian) {
                $query->where('veterinarian_id', $veterinarian->id)
                      ->orderBy('created_at', 'desc');
            }
        ])
        ->whereHas('appointments', function($query) use ($veterinarian) {
            $query->where('veterinarian_id', $veterinarian->id);
        })
        ->findOrFail($id);

        return view('veterinarian.patients.show', compact('pet'));
    }
}
