<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\Prescription;
use App\Models\ClinicSetting;
use App\Models\QrScanLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
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

        $submissionToken = (string) Str::uuid();
        session(['pet_create_submission_token' => $submissionToken]);

        return view('customer.pets.create', compact('submissionToken'));
    }
    
    public function store(Request $request)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }

        $sessionToken = session('pet_create_submission_token');
        $requestToken = (string) $request->input('submission_token');

        if (!$sessionToken || !$requestToken || !hash_equals($sessionToken, $requestToken)) {
            return redirect()->route('customer.pets.create')
                ->with('error', 'Your form session expired. Please fill in the pet details again.');
        }

        $submissionLock = Cache::lock('pet-create:' . $user->id . ':' . $requestToken, 10);
        if (!$submissionLock->get()) {
            return redirect()->route('customer.pets.index')
                ->with('info', 'Pet registration is already being processed.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'species' => 'required|in:Dog,Cat,Bird,Reptile,Other',
                'breed' => 'nullable|string|max:255',
                'gender' => 'required|in:Male,Female',
                'birth_date' => 'nullable|date|before_or_equal:today',
                'weight' => 'required|numeric|min:0.01|max:999.99',
                'color' => 'nullable|string|max:255',
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

            $petData = $request->only([
                'name',
                'species',
                'breed',
                'gender',
                'birth_date',
                'weight',
                'color',
                'medical_history',
            ]);
            $petData['owner_id'] = $petOwner->id;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = Str::random(40) . '.' . $photo->getClientOriginalExtension();
                $directory = public_path('uploads/pets');
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }
                $photo->move($directory, $filename);
                $petData['photo_path'] = 'uploads/pets/' . $filename;
            }

            Pet::create($petData);

            // Automatically change user role to pet_owner since they now have pets
            $user->update(['role' => 'pet_owner']);

            session()->forget('pet_create_submission_token');

            return redirect()->route('customer.pets.index')
                ->with('success', 'Pet registered successfully!');
        } finally {
            optional($submissionLock)->release();
        }
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
        $pet->load(['vaccinations', 'prescriptions.assignedStaff', 'medicalRecords']);
        
        return view('customer.pets.show', compact('pet'));
    }

    public function publicQr($id)
    {
        $pet = Pet::with('owner.user')->findOrFail($id);
        $scanUrl = route('pets.qr.records', ['id' => $pet->id]);

        return view('pets.qr-public', compact('pet', 'scanUrl'));
    }

    public function publicMedicalRecords($id)
    {
        $pet = Pet::with([
            'owner.user',
            'medicalRecords' => function ($query) {
                $query->with('veterinarian')->orderBy('visit_date', 'desc');
            },
            'prescriptions' => function ($query) {
                $query->with(['medicalRecord.veterinarian', 'assignedStaff'])->orderBy('created_at', 'desc');
            },
            'vaccinations' => function ($query) {
                $query->orderBy('administered_date', 'desc');
            },
            'chronicConditions' => function ($query) {
                $query->orderBy('diagnosed_date', 'desc');
            },
            'allergies' => function ($query) {
                $query->orderBy('diagnosed_date', 'desc');
            },
        ])->findOrFail($id);

        $sessionUsername = session('username');
        if ($sessionUsername) {
            $scanner = User::where('username', $sessionUsername)->first();
            if ($scanner) {
                QrScanLog::safeLog([
                    'scan_type' => 'pet',
                    'pet_id' => $pet->id,
                    'scanned_by' => $scanner->id,
                    'scan_timestamp' => now(),
                ]);
            }
        }

        return view('pets.scan-medical-records', compact('pet'));
    }

    public function qrCode($id)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }

        view()->share('user', $user);

        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (! $petOwner) {
            return redirect()->route('customer.pets.index')->with('error', 'No pet owner record found for your account.');
        }

        $pet = $petOwner->pets()->findOrFail($id);
        $scanUrl = route('pets.qr.records', ['id' => $pet->id]);

        return view('customer.pets.qr', compact('pet', 'scanUrl'));
    }

    public function qrMedicalRecords($id)
    {
        return redirect()->route('pets.qr.records', ['id' => $id]);
    }

    public function printPrescription($petId, $prescriptionId)
    {
        $user = $this->authenticateUser();
        if ($user instanceof \Illuminate\Http\RedirectResponse) {
            return $user;
        }

        view()->share('user', $user);

        $petOwner = PetOwner::where('user_id', $user->id)->first();
        if (! $petOwner) {
            return redirect()->route('customer.pets.index')->with('error', 'No pet owner record found for your account.');
        }

        $pet = Pet::with('owner.user')
            ->where('owner_id', $petOwner->id)
            ->findOrFail($petId);

        $prescription = Prescription::with([
            'medicalRecord.pet.owner.user',
            'medicalRecord.veterinarian',
            'assignedStaff',
            'dispensedBy',
        ])
            ->where('id', $prescriptionId)
            ->whereHas('medicalRecord', function ($query) use ($pet) {
                $query->where('pet_id', $pet->id);
            })
            ->firstOrFail();

        $clinicSetting = ClinicSetting::current();

        return view('customer.prescriptions.print', compact('pet', 'prescription', 'clinicSetting'));
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
            'birth_date' => 'nullable|date|before_or_equal:today',
            'weight' => 'required|numeric|min:0.01|max:999.99',
            'color' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'medical_history' => 'nullable|string',
        ]);

        $petData = $request->only([
            'name',
            'species',
            'breed',
            'gender',
            'birth_date',
            'weight',
            'color',
            'medical_history',
        ]);
        
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
        
        $pet->delete();
        
        return redirect()->route('customer.pets.index')
            ->with('success', 'Pet removed successfully!');
    }
}
