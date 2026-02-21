<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\InventoryItem;
use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VaccinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pets = Pet::with([
            'owner.user',
            'vaccinations' => function($query) {
                $query->orderBy('administered_date', 'desc');
            },
            'appointments' => function ($query) {
                $query->where('type', 'vaccination');
            },
        ])
            ->where(function ($query) {
                $query->has('vaccinations')
                    ->orWhereHas('appointments', function ($appointmentQuery) {
                        $appointmentQuery->where('type', 'vaccination');
                    });
            })
            ->paginate(10);

        $pets->getCollection()->transform(function ($pet) {
            $appointmentCount = $pet->appointments ? $pet->appointments->count() : 0;
            $pet->vaccination_appointment_count = $appointmentCount;
            $pet->vaccination_total_count = $pet->vaccinations->count() + $appointmentCount;
            return $pet;
        });

        return view('admin.vaccinations.index', compact('pets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pets = Pet::with('owner.user')->get();
        $vaccineItems = InventoryItem::where('category', 'vaccine')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $veterinarians = User::where('role', 'veterinarian')->orderBy('first_name')->get();
        $selectedPetId = request()->query('pet_id');

        return view('admin.vaccinations.create', compact('pets', 'vaccineItems', 'veterinarians', 'selectedPetId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'administered_date' => 'required|date',
            'administered_by' => 'required|exists:users,id',
            'next_due_date' => 'nullable|date|after_or_equal:administered_date',
            'batch_number' => 'nullable|string|max:255',
            'dose_number' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date|after_or_equal:administered_date',
            'notes' => 'nullable|string',
        ], [
            'administered_by.required' => 'Please select who administered the vaccine.',
            'administered_by.exists' => 'The selected veterinarian is invalid.',
        ]);

        $inventoryItem = InventoryItem::find($validated['inventory_item_id']);
        if (!$inventoryItem || $inventoryItem->category !== 'vaccine') {
            return back()
                ->withErrors(['inventory_item_id' => 'Please select a valid vaccine from inventory.'])
                ->withInput();
        }

        $vaccination = Vaccination::create($validated);
        $vaccination->loadMissing('pet');

        if (!empty($vaccination->next_due_date)) {
            $petName = $vaccination->pet?->name ?? 'Pet';
            $notificationService->sendToRole(
                'veterinarian',
                Notification::TYPE_VACCINATION,
                'Vaccination Due Scheduled',
                $petName . ' has a vaccination due on ' . $vaccination->next_due_date . '.',
                [
                    'reference_type' => 'vaccination',
                    'reference_id' => $vaccination->id,
                    'action_url' => route('admin.vaccinations.show', $vaccination->id),
                ]
            );
        }

        return redirect()->route('admin.vaccinations.index')
            ->with('success', 'Vaccination recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vaccination = Vaccination::with(['pet.owner.user', 'inventoryItem', 'administeredBy'])
            ->findOrFail($id);

        return view('admin.vaccinations.show', compact('vaccination'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vaccination = Vaccination::with(['pet.owner.user', 'inventoryItem', 'administeredBy'])->findOrFail($id);
        $pets = Pet::with('owner.user')->get();
        $vaccineItems = InventoryItem::where('category', 'vaccine')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $veterinarians = User::where('role', 'veterinarian')->orderBy('first_name')->get();
        $selectedVaccineItemId = $vaccination->inventory_item_id;

        return view('admin.vaccinations.edit', compact('vaccination', 'pets', 'vaccineItems', 'veterinarians', 'selectedVaccineItemId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vaccination = Vaccination::findOrFail($id);

        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'administered_date' => 'required|date',
            'administered_by' => 'required|exists:users,id',
            'next_due_date' => 'nullable|date|after_or_equal:administered_date',
            'batch_number' => 'nullable|string|max:255',
            'dose_number' => 'nullable|integer|min:1',
            'expiry_date' => 'nullable|date|after_or_equal:administered_date',
            'notes' => 'nullable|string',
        ], [
            'administered_by.required' => 'Please select who administered the vaccine.',
            'administered_by.exists' => 'The selected veterinarian is invalid.',
        ]);

        $inventoryItem = InventoryItem::find($validated['inventory_item_id']);
        if (!$inventoryItem || $inventoryItem->category !== 'vaccine') {
            return back()
                ->withErrors(['inventory_item_id' => 'Please select a valid vaccine from inventory.'])
                ->withInput();
        }

        $vaccination->update($validated);

        return redirect()->route('admin.vaccinations.show', $vaccination->id)
            ->with('success', 'Vaccination updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vaccination = Vaccination::findOrFail($id);
        $vaccination->delete();

        return redirect()->route('admin.vaccinations.index')
            ->with('success', 'Vaccination deleted successfully!');
    }

    /**
     * Display vaccinations for a specific pet.
     */
    public function byPet($petId)
    {
        $pet = Pet::findOrFail($petId);
        $vaccinations = $pet->vaccinations()
            ->with(['inventoryItem', 'administeredBy'])
            ->orderBy('administered_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $appointmentVaccinations = $pet->appointments()
            ->where('type', 'vaccination')
            ->orderBy('appointment_date', 'desc')
            ->get();

        $virtualVaccinations = $appointmentVaccinations->map(function ($appointment) {
            $vaccination = new Vaccination();
            $vaccination->setRelation('pet', $appointment->pet);
            $vaccination->setRelation('appointment', $appointment);
            $vaccination->setAttribute('administered_date', $appointment->appointment_date);
            $vaccination->setAttribute('next_due_date', null);
            $vaccination->setAttribute('is_virtual', true);
            $vaccination->setAttribute('status', $appointment->status);
            return $vaccination;
        });

        $allVaccinations = $vaccinations
            ->concat($virtualVaccinations)
            ->sortByDesc(function ($item) {
                $date = $item->administered_date ?? optional($item->appointment)->appointment_date;
                return $date ? $date->timestamp : 0;
            })
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pageItems = $allVaccinations->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $vaccinations = new LengthAwarePaginator(
            $pageItems,
            $allVaccinations->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.vaccinations.pet', compact('pet', 'vaccinations'));
    }
}
