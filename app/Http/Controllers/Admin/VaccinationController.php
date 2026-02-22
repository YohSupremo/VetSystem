<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\InventoryItem;
use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\PetVaccination;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\PetAllergy;
use App\Models\StaffSchedule;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

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
                $query->where('type', 'vaccination')
                    ->whereNotIn('status', ['completed', 'cancelled', 'no_show']);
            },
        ])
            ->where(function ($query) {
                $query->has('vaccinations')
                    ->orWhereHas('appointments', function ($appointmentQuery) {
                        $appointmentQuery->where('type', 'vaccination')
                            ->whereNotIn('status', ['completed', 'cancelled', 'no_show']);
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
        
        // Get scheduled veterinarians for current day/time
        $scheduledStaffIds = StaffSchedule::getCurrentScheduledStaffIds();
        $veterinarians = User::where('role', 'veterinarian')
            ->whereIn('id', $scheduledStaffIds)
            ->orderBy('first_name')
            ->get();
        
        $selectedPetId = request()->query('pet_id');
        $selectedAppointmentId = request()->query('appointment_id');

        $selectedAdministeredDate = request()->query('administered_date', date('Y-m-d'));
        $selectedVeterinarianId = request()->query('administered_by');

        $appointmentContext = null;
        if (!empty($selectedAppointmentId)) {
            $appointmentContext = Appointment::with(['pet.owner.user', 'veterinarian'])
                ->where('id', $selectedAppointmentId)
                ->where('type', 'vaccination')
                ->first();

            if ($appointmentContext) {
                $selectedPetId = $appointmentContext->pet_id;
                $selectedAdministeredDate = optional($appointmentContext->appointment_date)->format('Y-m-d')
                    ?? $selectedAdministeredDate;
                $selectedVeterinarianId = $appointmentContext->veterinarian_id ?: $selectedVeterinarianId;
            }
        }

        $allergyMap = PetAllergy::query()
            ->where('is_active', 1)
            ->orderByDesc('severity')
            ->orderBy('allergen')
            ->get()
            ->groupBy('pet_id')
            ->map(function ($items) {
                return $items->map(function ($allergy) {
                    return [
                        'allergen' => $allergy->allergen,
                        'reaction_type' => $allergy->reaction_type,
                        'severity' => $allergy->severity,
                    ];
                })->values()->all();
            })->all();

        return view('admin.vaccinations.create', compact(
            'pets',
            'vaccineItems',
            'veterinarians',
            'selectedPetId',
            'selectedAppointmentId',
            'selectedAdministeredDate',
            'selectedVeterinarianId',
            'appointmentContext',
            'allergyMap'
        ));
    }

    public function acceptAppointment($appointmentId)
    {
        $appointment = Appointment::with(['pet'])
            ->where('id', $appointmentId)
            ->where('type', 'vaccination')
            ->firstOrFail();

        if (in_array($appointment->status, ['cancelled', 'no_show'], true)) {
            return back()->withErrors(['error' => 'Cancelled or no-show appointments cannot be accepted.']);
        }

        return redirect()->route('admin.vaccinations.create', [
            'pet_id' => $appointment->pet_id,
            'appointment_id' => $appointment->id,
            'administered_date' => optional($appointment->appointment_date)->format('Y-m-d'),
            'administered_by' => $appointment->veterinarian_id,
        ])->with('success', 'Appointment accepted. Continue by recording the vaccination details.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'administered_date' => 'required|date',
            'administered_by' => 'required|exists:users,id',
            'next_due_date' => 'nullable|date|after_or_equal:administered_date',
            'batch_number' => 'nullable|string|max:255',
            'dose_number' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after_or_equal:administered_date',
            'notes' => 'nullable|string',
        ], [
            'administered_by.required' => 'Please select who administered the vaccine.',
            'administered_by.exists' => 'The selected veterinarian is invalid.',
            'dose_number.required' => 'Please select a dose number.',
        ]);

        $inventoryItem = InventoryItem::find($validated['inventory_item_id']);
        if (!$inventoryItem || $inventoryItem->category !== 'vaccine') {
            return back()
                ->withErrors(['inventory_item_id' => 'Please select a valid vaccine from inventory.'])
                ->withInput();
        }

        $appointmentId = $validated['appointment_id'] ?? null;
        unset($validated['appointment_id']);

        $sourceAppointment = null;
        if ($appointmentId) {
            $sourceAppointment = Appointment::where('id', $appointmentId)
                ->where('type', 'vaccination')
                ->first();

            if (!$sourceAppointment) {
                return back()->withErrors(['appointment_id' => 'Selected appointment is invalid.'])->withInput();
            }

            if ((int) $sourceAppointment->pet_id !== (int) $validated['pet_id']) {
                return back()->withErrors(['appointment_id' => 'Appointment does not belong to the selected pet.'])->withInput();
            }
        }

        $vaccination = DB::transaction(function () use ($validated, $sourceAppointment, $inventoryItem) {
            $vaccination = Vaccination::create($validated);

            $this->deductVaccineStockForVaccination($inventoryItem, $vaccination);

            if ($sourceAppointment && !in_array($sourceAppointment->status, ['completed', 'cancelled', 'no_show'], true)) {
                $sourceAppointment->update(['status' => 'completed']);
            }

            $this->ensureVaccinationInvoice($vaccination);

            return $vaccination;
        });

        $vaccination->loadMissing(['pet.owner', 'inventoryItem']);

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

    public function processPayment($id)
    {
        $vaccination = Vaccination::with(['pet.owner', 'inventoryItem'])->findOrFail($id);
        $invoice = $this->ensureVaccinationInvoice($vaccination);
        $invoice->load(['invoiceItems', 'payments']);

        if ($invoice->status === 'cancelled') {
            return back()->withErrors(['error' => 'Cannot mark a cancelled vaccination invoice as paid.']);
        }

        if ($invoice->is_paid) {
            return back()->with('success', 'Vaccination invoice is already fully paid.');
        }

        $balance = (float) $invoice->balance;
        if ($balance <= 0) {
            $invoice->update(['status' => 'paid']);
            return back()->with('success', 'Vaccination invoice marked as paid.');
        }

        Payment::create([
            'invoice_id' => $invoice->id,
            'payment_date' => now(),
            'amount' => $balance,
            'payment_method' => 'cash',
            'reference_number' => null,
            'received_by' => auth()->id(),
            'notes' => 'Paid from vaccination record #' . $vaccination->id,
        ]);

        $invoice->load(['invoiceItems', 'payments']);
        $invoice->update(['status' => $invoice->balance <= 0 ? 'paid' : 'partial']);

        return back()->with('success', 'Vaccination payment recorded successfully.');
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
        
        // Get scheduled veterinarians for current day/time
        $scheduledStaffIds = StaffSchedule::getCurrentScheduledStaffIds();
        $veterinarians = User::where('role', 'veterinarian')
            ->whereIn('id', $scheduledStaffIds)
            ->orderBy('first_name')
            ->get();
        
        $selectedVaccineItemId = $vaccination->inventory_item_id;
        $activeAllergies = PetAllergy::query()
            ->where('pet_id', $vaccination->pet_id)
            ->where('is_active', 1)
            ->orderByDesc('severity')
            ->orderBy('allergen')
            ->get();

        return view('admin.vaccinations.edit', compact('vaccination', 'pets', 'vaccineItems', 'veterinarians', 'selectedVaccineItemId', 'activeAllergies'));
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
            'dose_number' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after_or_equal:administered_date',
            'notes' => 'nullable|string',
        ], [
            'administered_by.required' => 'Please select who administered the vaccine.',
            'administered_by.exists' => 'The selected veterinarian is invalid.',
            'dose_number.required' => 'Please select a dose number.',
        ]);

        $inventoryItem = InventoryItem::find($validated['inventory_item_id']);
        if (!$inventoryItem || $inventoryItem->category !== 'vaccine') {
            return back()
                ->withErrors(['inventory_item_id' => 'Please select a valid vaccine from inventory.'])
                ->withInput();
        }

        $vaccination->update($validated);

        $vaccination->loadMissing(['pet.owner', 'inventoryItem']);
        $this->ensureVaccinationInvoice($vaccination);

        return redirect()->route('admin.vaccinations.show', $vaccination->id)
            ->with('success', 'Vaccination updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vaccination = Vaccination::findOrFail($id);
        $invoice = $this->findVaccinationInvoice($vaccination);

        if ($invoice && !$invoice->is_paid && $invoice->status !== 'cancelled') {
            $invoice->update(['status' => 'cancelled']);
        }

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
            ->whereNotIn('status', ['completed', 'cancelled', 'no_show'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        $virtualVaccinations = $appointmentVaccinations->map(function ($appointment) {
            $vaccination = new Vaccination();
            $vaccination->setRelation('pet', $appointment->pet);
            $vaccination->setRelation('appointment', $appointment);
            $vaccination->setAttribute('administered_date', null);
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

        $pageItems = $pageItems->map(function ($item) {
            $isVirtual = (bool) $item->getAttribute('is_virtual');
            if ($isVirtual) {
                return $item;
            }

            $invoice = $this->findVaccinationInvoice($item);
            if ($invoice) {
                $item->setAttribute('billing_invoice', $invoice);
            }

            return $item;
        });

        $vaccinations = new LengthAwarePaginator(
            $pageItems,
            $allVaccinations->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.vaccinations.pet', compact('pet', 'vaccinations'));
    }

    private function ensureVaccinationInvoice(Vaccination|PetVaccination $vaccination): Invoice
    {
        $existing = $this->findVaccinationInvoice($vaccination);
        if ($existing) {
            return $this->syncVaccinationInvoice($vaccination, $existing);
        }

        $vaccination->loadMissing(['pet.owner']);
        $ownerId = optional($vaccination->pet)->owner_id;

        if (!$ownerId) {
            abort(422, 'Cannot create vaccination invoice because pet owner is missing.');
        }

        $issueDate = $vaccination->administered_date
            ? Carbon::parse($vaccination->administered_date)->toDateString()
            : now()->toDateString();

        $invoice = new Invoice([
            'owner_id' => $ownerId,
            'pet_id' => $vaccination->pet_id,
            'invoice_prefix' => 'INV',
            'issue_date' => $issueDate,
            'due_date' => $issueDate,
            'tax_rate' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
            'notes' => 'Vaccination invoice for record #' . $vaccination->id . ' ' . $this->vaccinationInvoiceTag($vaccination->id),
        ]);

        $invoice->invoice_number = $invoice->generateInvoiceNumber();
        $invoice->save();

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'vaccination',
            'description' => $this->vaccinationInvoiceDescription($vaccination),
            'quantity' => 1,
            'unit_price' => $this->vaccinationUnitPrice($vaccination),
        ]);

        return $this->syncVaccinationInvoice($vaccination, $invoice->load(['invoiceItems', 'payments']));
    }

    private function findVaccinationInvoice(Vaccination|PetVaccination $vaccination): ?Invoice
    {
        $tag = $this->vaccinationInvoiceTag($vaccination->id);

        $invoice = Invoice::with(['invoiceItems', 'payments'])
            ->where('notes', 'like', '%' . $tag . '%')
            ->orderByDesc('id')
            ->first();

        if (!$invoice instanceof Invoice) {
            return null;
        }

        return $invoice;
    }

    private function syncVaccinationInvoice(Vaccination|PetVaccination $vaccination, Invoice $invoice): Invoice
    {
        $invoice->loadMissing(['invoiceItems', 'payments']);

        $description = $this->vaccinationInvoiceDescription($vaccination);
        $unitPrice = $this->vaccinationUnitPrice($vaccination);
        $issueDate = $vaccination->administered_date
            ? Carbon::parse($vaccination->administered_date)->toDateString()
            : now()->toDateString();

        $item = $invoice->invoiceItems->firstWhere('item_type', 'vaccination')
            ?? $invoice->invoiceItems->first();

        if ($item) {
            $updates = [];

            if ((string) $item->item_type !== 'vaccination') {
                $updates['item_type'] = 'vaccination';
            }

            if ((int) $item->quantity !== 1) {
                $updates['quantity'] = 1;
            }

            if ((float) $item->unit_price !== (float) $unitPrice) {
                $updates['unit_price'] = $unitPrice;
            }

            if ((string) $item->description !== (string) $description) {
                $updates['description'] = $description;
            }

            if (!empty($updates)) {
                $item->update($updates);
            }
        } else {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'vaccination',
                'description' => $description,
                'quantity' => 1,
                'unit_price' => $unitPrice,
            ]);
        }

        $invoiceUpdates = [];

        $currentIssueDate = $invoice->issue_date ? Carbon::parse($invoice->issue_date)->toDateString() : null;
        $currentDueDate = $invoice->due_date ? Carbon::parse($invoice->due_date)->toDateString() : null;

        if ((string) $currentIssueDate !== (string) $issueDate) {
            $invoiceUpdates['issue_date'] = $issueDate;
        }

        if ((string) $currentDueDate !== (string) $issueDate) {
            $invoiceUpdates['due_date'] = $issueDate;
        }

        if ((int) $invoice->pet_id !== (int) $vaccination->pet_id) {
            $invoiceUpdates['pet_id'] = $vaccination->pet_id;
        }

        if (!empty($invoiceUpdates)) {
            $invoice->update($invoiceUpdates);
            $invoice->refresh();
        }

        $invoice->load(['invoiceItems', 'payments']);

        if ($invoice->status !== 'cancelled') {
            $targetStatus = $invoice->total_amount <= 0
                ? 'pending'
                : ($invoice->balance <= 0
                    ? 'paid'
                    : ($invoice->paid_amount > 0 ? 'partial' : 'pending'));

            if ($invoice->status !== $targetStatus) {
                $invoice->update(['status' => $targetStatus]);
                $invoice->refresh();
            }
        }

        return $invoice->load(['invoiceItems', 'payments']);
    }

    private function vaccinationUnitPrice(Vaccination|PetVaccination $vaccination): float
    {
        $vaccination->loadMissing('inventoryItem');
        return round((float) (optional($vaccination->inventoryItem)->unit_price ?? 0), 2);
    }

    private function vaccinationInvoiceDescription(Vaccination|PetVaccination $vaccination): string
    {
        $vaccination->loadMissing(['pet', 'inventoryItem']);
        $petName = optional($vaccination->pet)->name ?? 'Pet';
        $vaccineName = optional($vaccination->inventoryItem)->name ?? 'Vaccine';
        $date = $vaccination->administered_date
            ? Carbon::parse($vaccination->administered_date)->format('Y-m-d')
            : now()->format('Y-m-d');

        return 'Vaccination for ' . $petName . ' (' . $vaccineName . ') on ' . $date;
    }

    private function vaccinationInvoiceTag(int $vaccinationId): string
    {
        return '[VACCINATION_ID:' . $vaccinationId . ']';
    }

    private function deductVaccineStockForVaccination(InventoryItem $inventoryItem, Vaccination $vaccination): void
    {
        $requiredQuantity = 1;

        /** @var \Illuminate\Database\Eloquent\Collection<int, InventoryStock> $stocks */
        $stocks = InventoryStock::query()
            ->where('item_id', $inventoryItem->id)
            ->where('quantity', '>', 0)
            ->orderByRaw('ISNULL(expiry_date), expiry_date ASC')
            ->orderBy('created_at', 'asc')
            ->lockForUpdate()
            ->get();

        $available = (int) $stocks->sum('quantity');
        if ($available < $requiredQuantity) {
            throw ValidationException::withMessages([
                'inventory_item_id' => 'Insufficient stock for the selected vaccine.',
            ]);
        }

        $remaining = $requiredQuantity;
        foreach ($stocks as $stock) {
            if (!$stock instanceof InventoryStock) {
                continue;
            }

            if ($remaining <= 0) {
                break;
            }

            $deducted = min((int) $stock->quantity, $remaining);
            if ($deducted <= 0) {
                continue;
            }

            $stock->decrement('quantity', $deducted);

            InventoryTransaction::create([
                'stock_id' => $stock->id,
                'type' => 'out',
                'quantity' => $deducted,
                'reference' => 'Vaccination #' . $vaccination->id,
                'performed_by' => auth()->id(),
                'notes' => 'Vaccine used for pet vaccination record #' . $vaccination->id,
                'transaction_date' => now(),
            ]);

            $remaining -= $deducted;
        }
    }
}
