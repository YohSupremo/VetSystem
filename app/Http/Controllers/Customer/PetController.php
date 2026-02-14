<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\PetOwner;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PetController extends Controller
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
            // Create pet owner record if it doesn't exist
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        
        $pets = $petOwner->pets()->orderBy('created_at', 'desc')->get();
        
        return view('customer.pets.index', compact('pets'));
    }
    
    public function create()
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        return view('customer.pets.create');
    }
    
    public function store(Request $request)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|in:Dog,Cat,Bird,Reptile,Other',
            'breed' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female',
            'dob' => 'nullable|date|before_or_equal:today',
            'weight' => 'required|numeric|min:0.01|max:999.99',
            'color' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255|unique:pets,registration_number',
            'photo' => 'nullable|image|max:2048', // 2MB max
            'medical_history' => 'nullable|string',
        ]);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (!$petOwner) {
            $petOwner = PetOwner::create([
                'user_id' => $user->id,
                'notes' => null
            ]);
        }
        
        $petData = $request->except('photo');
        $petData['owner_id'] = $petOwner->id;
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            \Log::info('Customer Pet update: photo file detected', ['pet_id' => $pet->id ?? null, 'user_id' => $user->id ?? null]);
            $photo = $request->file('photo');
            \Log::info('Customer Pet update: uploaded file info', ['originalName' => $photo->getClientOriginalName(), 'size' => $photo->getSize(), 'mime' => $photo->getMimeType()]);
            $filename = Str::random(40) . '.' . $photo->getClientOriginalExtension();
            $directory = public_path('uploads/pets');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            $photo->move($directory, $filename);
            $petData['photo_path'] = 'uploads/pets/' . $filename;
        }
        
        $pet = Pet::create($petData);
        
        // Automatically change user role to pet_owner since they now have pets
        $user->update(['role' => 'pet_owner']);
        
        return redirect()->route('customer.pets.index')
            ->with('success', 'Pet registered successfully!');
    }
    
    public function show($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($id);
        
        // Load relationships
        $pet->load(['vaccinations', 'prescriptions', 'medicalRecords']);
        
        return view('customer.pets.show', compact('pet'));
    }
    
    public function edit($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        view()->share('user', $user);
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($id);
        
        return view('customer.pets.edit', compact('pet'));
    }
    
    public function update(Request $request, $id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|in:Dog,Cat,Bird,Reptile,Other',
            'breed' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female',
            'dob' => 'nullable|date|before_or_equal:today',
            'weight' => 'required|numeric|min:0.01|max:999.99',
            'color' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255|unique:pets,registration_number,' . $pet->id,
            'photo' => 'nullable|image|max:2048',
            'medical_history' => 'nullable|string',
        ]);

        $petData = $request->except('photo');
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($pet->photo_path) {
                File::delete(public_path($pet->photo_path));
            }

            $photo = $request->file('photo');
            $filename = Str::random(40) . '.' . $photo->getClientOriginalExtension();
            $directory = public_path('uploads/pets');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            $photo->move($directory, $filename);
            $petData['photo_path'] = 'uploads/pets/' . $filename;
        }
        
        \Log::info('Customer Pet update: before update', ['pet_id' => $pet->id, 'photo_path' => $pet->photo_path]);
        $pet->update($petData);
        \Log::info('Customer Pet update: after update', ['pet_id' => $pet->id, 'photo_path' => $pet->photo_path]);

        return redirect()->route('customer.pets.show', $pet->id)
            ->with('success', 'Pet information updated successfully!');
    }
    
    public function destroy($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }
        
        $petOwner = PetOwner::where('user_id', $user->id)->first();
        $pet = $petOwner->pets()->findOrFail($id);
        
        // Delete photo if exists
        if ($pet->photo_path) {
            File::delete(public_path($pet->photo_path));
        }
        
        $pet->delete();
        
        return redirect()->route('customer.pets.index')
            ->with('success', 'Pet removed successfully!');
    }
}
