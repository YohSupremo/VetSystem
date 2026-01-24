<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Pet;
use Carbon\Carbon;

class GroomingController extends BaseController
{
    /**
     * Display a listing of grooming services.
     */
    public function index()
    {
        $appointments = Appointment::with(['pet', 'pet.owner', 'services'])->where('type', 'grooming')->paginate(10);
        $todayAppointments = Appointment::where('type', 'grooming')->whereDate('appointment_date', Carbon::today())->count();
        $completedAppointments = Appointment::where('type', 'grooming')->where('status', 'completed')->count();
        $pets = Pet::with('owner')->get();
        
        // Dummy services data
        $services = collect([
            (object)['id' => 1, 'name' => 'Full Bath', 'description' => 'Complete grooming bath', 'price' => 45.00, 'icon' => 'fa-shower'],
            (object)['id' => 2, 'name' => 'Nail Trim', 'description' => 'Professional nail trimming', 'price' => 15.00, 'icon' => 'fa-cut'],
            (object)['id' => 3, 'name' => 'Hair Cut', 'description' => 'Full hair cut and styling', 'price' => 55.00, 'icon' => 'fa-scissors'],
        ]);
        $allServices = $services;
        $servicesCount = count($services);
        
        // Dummy groomers data
        $groomers = collect([
            (object)['id' => 1, 'user' => (object)['first_name' => 'John', 'last_name' => 'Doe']],
            (object)['id' => 2, 'user' => (object)['first_name' => 'Jane', 'last_name' => 'Smith']],
            (object)['id' => 3, 'user' => (object)['first_name' => 'Mike', 'last_name' => 'Johnson']],
        ]);
        $groomersCount = count($groomers);
        
        return view('admin.grooming.index', compact('appointments', 'todayAppointments', 'completedAppointments', 'services', 'pets', 'allServices', 'groomers', 'servicesCount', 'groomersCount'));
    }

    /**
     * Show the form for creating a new grooming service.
     */
    public function create()
    {
        return view('admin.grooming.create');
    }

    /**
     * Store a newly created grooming service in storage.
     */
    public function store(Request $request)
    {
        // Placeholder for grooming store logic
        return redirect()->route('admin.grooming.index')->with('success', 'Grooming created successfully.');
    }

    /**
     * Display the specified grooming service.
     */
    public function show($id)
    {
        return view('admin.grooming.show');
    }

    /**
     * Show the form for editing the specified grooming service.
     */
    public function edit($id)
    {
        return view('admin.grooming.edit');
    }

    /**
     * Update the specified grooming service in storage.
     */
    public function update(Request $request, $id)
    {
        // Placeholder for grooming update logic
        return redirect()->route('admin.grooming.index')->with('success', 'Grooming updated successfully.');
    }

    /**
     * Remove the specified grooming service from storage.
     */
    public function destroy($id)
    {
        // Placeholder for grooming delete logic
        return redirect()->route('admin.grooming.index')->with('success', 'Grooming deleted successfully.');
    }
}
