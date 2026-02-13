<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    private function authenticateUser()
    {
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }
        
        $user = User::where('username', $username)->first();
       if (!$user || ($user->role !== 'pet_owner' && $user->role !== 'registered_user')) {
            return redirect('/login')->with('error', 'Access denied');
        }
        
        return $user;
    }
    
    public function index()
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        // Get upcoming appointments (including today and cancelled ones)
        $upcomingAppointments = Appointment::whereIn('pet_id', $petIds)
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'scheduled', 'in_progress', 'cancelled'])
            ->orderBy('appointment_date', 'asc')
            ->get();
        
        // Get past appointments
        $pastAppointments = Appointment::whereIn('pet_id', $petIds)
            ->where('appointment_date', '<=', now())
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('appointment_date', 'desc')
            ->get();
        
        return view('customer.appointments.index', compact('upcomingAppointments', 'pastAppointments'));
    }
    
    public function create()
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            // Create pet owner record if it doesn't exist
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        
        $pets = $petOwner->pets()->orderBy('name')->get();
        
        if ($pets->isEmpty()) {
            return redirect()->route('customer.pets.create')
                ->with('info', 'Please register a pet first before booking an appointment.');
        }
        
        // Appointment types
        $appointmentTypes = [
            'consultation' => 'General Consultation',
            'vaccination' => 'Vaccination',
            'surgery' => 'Surgery',
            'grooming' => 'Grooming',
            'boarding' => 'Boarding',
            'follow_up' => 'Follow-up Visit',
            'other' => 'Other'
        ];
        
        // Generate available time slots (next 14 days)
        $availableSlots = $this->generateAvailableSlots();
        
        return view('customer.appointments.create', compact('pets', 'appointmentTypes', 'availableSlots'));
    }
    
    public function store(Request $request)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|string',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'notes' => 'nullable|string'
        ]);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($request->pet_id);
        
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'veterinarian_id' => null, // Will be assigned by admin
            'appointment_date' => $validated['appointment_date'],
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => null,
            'type' => $validated['type'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null
        ]);
        
        return redirect()->route('customer.appointments.index')
            ->with('success', 'Appointment booked successfully! We will confirm your appointment shortly.');
    }
    
    public function show($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        $appointment = Appointment::whereIn('pet_id', $petIds)->findOrFail($id);
        $appointment->load(['pet', 'veterinarian']);
        
        return view('customer.appointments.show', compact('appointment'));
    }
    
    public function edit($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        $appointment = Appointment::whereIn('pet_id', $petIds)
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $pets = $petOwner->pets()->orderBy('name')->get();
        
        $appointmentTypes = [
            'consultation' => 'General Consultation',
            'vaccination' => 'Vaccination',
            'surgery' => 'Surgery',
            'grooming' => 'Grooming',
            'boarding' => 'Boarding',
            'follow_up' => 'Follow-up Visit',
            'other' => 'Other'
        ];
        
        return view('customer.appointments.edit', compact('appointment', 'pets', 'appointmentTypes'));
    }
    
    public function update(Request $request, $id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        $appointment = Appointment::whereIn('pet_id', $petIds)
            ->where('status', 'pending')
            ->findOrFail($id);
        
        // Combine date and time
        $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
        
        // Check if slot is still available (excluding current appointment)
        $existingAppointment = Appointment::where('appointment_date', $appointmentDateTime)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'cancelled')
            ->first();
            
        if ($existingAppointment) {
            return back()->withInput()->with('error', 'This time slot is no longer available. Please choose another time.');
        }
        
        $appointment->update([
            'pet_id' => $request->pet_id,
            'appointment_date' => $appointmentDateTime,
            'type' => $request->type,
            'notes' => $request->notes
        ]);
        
        return redirect()->route('customer.appointments.show', $appointment->id)
            ->with('success', 'Appointment updated successfully!');
    }
    
    public function cancel($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        $petIds = $petOwner->pets()->pluck('id');
        
        $appointment = Appointment::whereIn('pet_id', $petIds)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);
        
        $appointment->update(['status' => 'cancelled']);
        
        return redirect()->route('customer.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }
    
    private function generateAvailableSlots()
    {
        $slots = [];
        $startDate = Carbon::tomorrow();
        $endDate = Carbon::tomorrow()->addDays(14);
        
        // Working hours: 9 AM to 5 PM
        $workingHours = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'
        ];
        
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }
            
            $dateSlots = [];
            foreach ($workingHours as $time) {
                $dateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $time);
                
                // Check if slot is already booked
                $isBooked = Appointment::where('appointment_date', $dateTime)
                    ->where('status', '!=', 'cancelled')
                    ->exists();
                
                $dateSlots[] = [
                    'time' => $time,
                    'available' => !$isBooked
                ];
            }
            
            $slots[$date->format('Y-m-d')] = [
                'date' => $date->format('M d, Y'),
                'day_name' => $date->format('l'),
                'slots' => $dateSlots
            ];
        }
        
        return $slots;
    }
}
