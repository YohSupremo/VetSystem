<?php

namespace App\Http\Controllers\Admin;

use App\Models\CageAssignment;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Cage;
use Carbon\Carbon;

class BoardingController extends BaseController
{
    /**
     * Display a listing of boarding services.
     */
    public function index()
    {
        // Sync all cage statuses to ensure accuracy
        $allCages = Cage::all();
        foreach ($allCages as $cage) {
            $cage->syncStatus();
        }
        
        $query = CageAssignment::with(['pet.owner.user', 'cage']);
        
        // Search functionality
        if (request('search')) {
            $search = request('search');
            $query->whereHas('pet', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('cage', function ($q) use ($search) {
                $q->where('cage_code', 'like', '%' . $search . '%');
            });
        }
        
        $boardings = $query->orderBy('start_date', 'desc')->get();

        $appointmentBoardings = Appointment::with(['pet.owner.user'])
            ->where('type', 'boarding')
            ->whereIn('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])
            ->orderByDesc('appointment_date')
            ->get();

        $virtualBoardings = $appointmentBoardings->map(function ($appointment) {
            $boarding = new CageAssignment();
            $boarding->setRelation('pet', $appointment->pet);
            $boarding->setRelation('cage', null);
            $boarding->setRelation('appointment', $appointment);
            $boarding->setAttribute('start_date', optional($appointment->appointment_date)->toDateString());
            $boarding->setAttribute('end_date', optional($appointment->appointment_date)->toDateString());
            $boarding->setAttribute('check_in_time', $appointment->appointment_date);

            $mapped = $this->mapBoardingStatusFromAppointment($appointment->status);
            $boarding->setAttribute('derived_status', $mapped['text']);
            $boarding->setAttribute('derived_status_class', $mapped['class']);
            $boarding->setAttribute('is_virtual', true);

            return $boarding;
        });

        $boardings = $boardings
            ->concat($virtualBoardings)
            ->sortByDesc(function ($item) {
                $date = $item->start_date ?? optional($item->appointment)->appointment_date;
                return $date ? strtotime((string) $date) : 0;
            })
            ->values();

        $boardingIds = $boardings->pluck('id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
        $boardingBilling = [];

        if ($boardingIds->isNotEmpty()) {
            $boardingIdLookup = array_flip($boardingIds->all());

            $boardingInvoices = Invoice::with(['invoiceItems', 'payments'])
                ->where('notes', 'like', '%[BOARDING_ID:%')
                ->orderByDesc('id')
                ->get();

            foreach ($boardingInvoices as $invoice) {
                if (!preg_match('/\[BOARDING_ID:(\d+)\]/', (string) ($invoice->notes ?? ''), $matches)) {
                    continue;
                }

                $boardingId = (int) ($matches[1] ?? 0);
                if ($boardingId <= 0 || !isset($boardingIdLookup[$boardingId])) {
                    continue;
                }

                if (!isset($boardingBilling[$boardingId])) {
                    $boardingBilling[$boardingId] = $invoice;
                }
            }
        }
        
        // Calculate dashboard stats
        $currentBoardings = CageAssignment::active()->count();

        $currentBoardings += Appointment::where('type', 'boarding')
            ->where('status', 'in_progress')
            ->count();
        
        $availableCages = Cage::where('status', 'available')->count();
        
        $upcomingCheckouts = CageAssignment::whereDate('end_date', '=', now()->toDateString())->count();
        
        $pets = Pet::with('owner')->get();
        $cages = Cage::where('status', 'available')->get();
        
        return view('admin.boarding.index', compact('pets', 'boardings', 'currentBoardings', 'availableCages', 'upcomingCheckouts', 'cages', 'boardingBilling'));
    }

    private function mapBoardingStatusFromAppointment(string $appointmentStatus): array
    {
        return match ($appointmentStatus) {
            'completed' => ['text' => 'Completed', 'class' => 'secondary'],
            'cancelled', 'no_show' => ['text' => 'Cancelled', 'class' => 'danger'],
            'in_progress' => ['text' => 'Active', 'class' => 'success'],
            'pending', 'confirmed' => ['text' => 'Upcoming', 'class' => 'warning'],
            default => ['text' => 'Upcoming', 'class' => 'warning'],
        };
    }

   
    /**
     * Show the form for creating a new boarding service.
     */
    public function create()
    {
        $pets = Pet::with(['owner.user'])
        ->orderBy('name')
        ->get();

        $cages = Cage::with(['currentAssignment'])
        ->where('status', 'available')
        ->orderBy('cage_code')
        ->get();

         return view('admin.boarding.create', compact('pets', 'cages'));

    }

    public function createPass(Request $request)
    {
        $info = $request->validate([
            'cage_id'  => 'required|exists:cages,id',
            'pet_id'   => 'required|exists:pets,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'check_in_time'       => 'nullable|date_format:H:i',
            'check_out_time'      => 'nullable|date_format:H:i',
            'morning_feed_time'   => 'nullable|date_format:H:i',
            'afternoon_feed_time' => 'nullable|date_format:H:i',
            'evening_feed_time'   => 'nullable|date_format:H:i',
            'feeding_notes'       => 'nullable|string',
            'medication_notes'    => 'nullable|string',
            'medication_times'    => 'nullable|string',
            'daily_rate'          => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        $unpaidCompletedBoarding = $this->findUnpaidCompletedBoarding($info['pet_id']);
        if ($unpaidCompletedBoarding) {
            return back()
                ->withInput()
                ->withErrors([
                    'pet_id' => 'This pet has an unpaid completed boarding (ID #' . $unpaidCompletedBoarding->id . '). Please settle payment first before creating a new boarding.',
                ]);
        }

        $cage = Cage::find($info['cage_id']);

        if ($cage->status !== 'available') {
            return back()->withErrors(['cage_id' => 'This cage is not available.']);
        }

        $overlapping = $this->hasBlockingBoardingOverlap(
            (int) $info['pet_id'],
            (string) $info['start_date'],
            (string) $info['end_date']
        );

        if ($overlapping) {
            return back()->withErrors(['pet_id' => 'This pet is already assigned to a cage during the selected dates.']);
        }

        $cage->status = 'occupied';
        $cage->save();

        $assignment = CageAssignment::create([
            'cage_id' => $cage->id,
            'pet_id'  => $info['pet_id'],
            'start_date' => $info['start_date'],
            'end_date'   => $info['end_date'],
            'check_in_time' => $this->combineDateAndTime($info['start_date'], $info['check_in_time'] ?? null),
            'check_out_time' => $this->combineDateAndTime($info['end_date'], $info['check_out_time'] ?? null),
        ]);

        $feeding_times = array_filter([
            $info['morning_feed_time'] ?? null,
            $info['afternoon_feed_time'] ?? null,
            $info['evening_feed_time'] ?? null,
        ]);

        $assignment->update([
            'feeding_schedule' => !empty($feeding_times) ? 'timed' : 'as_needed',
            'feeding_times' => !empty($feeding_times) ? implode(',', $feeding_times) : null,
            'special_diet_notes' => $info['feeding_notes'] ?? null,
            'medication_instructions' => $info['medication_notes'] ?? null,
            'medication_times' => $info['medication_times'] ?? null,
            'daily_rate' => $info['daily_rate'] ?? null,
            'notes' => $info['notes'] ?? null,
        ]);

        $invoice = $this->ensureBoardingInvoice($assignment);

        // Sync cage status in case it was previously available
        $cage->syncStatus();

        return redirect()->route('admin.boarding.index')->with('success', 'Pet successfully assigned to cage and boarding created. Invoice ' . $invoice->invoice_number . ' has been generated.');
    }


    /**
     * Store a newly created boarding service in storage.
     */
    public function store(Request $request)
    {
        $info = $request->validate([
            'cage_id'  => 'required|exists:cages,id',
            'pet_id'   => 'required|exists:pets,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'check_in_time'       => 'nullable|date_format:H:i',
            'check_out_time'      => 'nullable|date_format:H:i',
            'feeding_schedule'    => 'nullable|string',
            'feeding_times'       => 'nullable|string',
            'special_diet_notes'  => 'nullable|string',
            'medication_instructions' => 'nullable|string',
            'medication_times'    => 'nullable|string',
            'daily_rate'          => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        $unpaidCompletedBoarding = $this->findUnpaidCompletedBoarding($info['pet_id']);
        if ($unpaidCompletedBoarding) {
            return back()
                ->withInput()
                ->withErrors([
                    'pet_id' => 'This pet has an unpaid completed boarding (ID #' . $unpaidCompletedBoarding->id . '). Please settle payment first before creating a new boarding.',
                ]);
        }

        $cage = Cage::find($info['cage_id']);

        if ($cage->status !== 'available') {
            return back()->withInput()->withErrors(['cage_id' => 'This cage is not available.']);
        }

        $overlapping = $this->hasBlockingBoardingOverlap(
            (int) $info['pet_id'],
            (string) $info['start_date'],
            (string) $info['end_date']
        );

        if ($overlapping) {
            return back()->withInput()->withErrors(['pet_id' => 'This pet is already assigned to a cage during the selected dates.']);
        }

        $cage->status = 'occupied';
        $cage->save();

        // Create cage assignment with all fields directly from form
        $boarding = CageAssignment::create([
            'cage_id' => $cage->id,
            'pet_id'  => $info['pet_id'],
            'start_date' => $info['start_date'],
            'end_date'   => $info['end_date'],
            'check_in_time' => $this->combineDateAndTime($info['start_date'], $info['check_in_time'] ?? null),
            'check_out_time' => null, // Will be set when pet checks out
            'feeding_schedule' => $info['feeding_schedule'] ?? null,
            'feeding_times' => $info['feeding_times'] ?? null,
            'special_diet_notes' => $info['special_diet_notes'] ?? null,
            'medication_instructions' => $info['medication_instructions'] ?? null,
            'medication_times' => $info['medication_times'] ?? null,
            'daily_rate' => $info['daily_rate'] ?? null,
            'notes' => $info['notes'] ?? null,
        ]);

        $invoice = $this->ensureBoardingInvoice($boarding);

        // Sync cage status to ensure accuracy
        $cage->syncStatus();

        return redirect()->route('admin.boarding.index')->with('success', 'Pet successfully assigned to cage and boarding created. Invoice ' . $invoice->invoice_number . ' has been generated.');
    }


    /**
     * Display the specified boarding service.
     */
    public function show($id)
    {
        $boarding = CageAssignment::with(['pet.owner.user', 'cage'])->findOrFail($id);
        $invoice = $this->findBoardingInvoice($boarding);

        return view('admin.boarding.show', compact('boarding', 'invoice'));
    }

    public function generateInvoice($id)
    {
        $boarding = CageAssignment::with(['pet.owner'])->findOrFail($id);
        $invoice = $this->ensureBoardingInvoice($boarding);

        return redirect()
            ->route('admin.boarding.show', $boarding->id)
            ->with('success', 'Boarding invoice ' . $invoice->invoice_number . ' is ready.');
    }

    public function processPayment(Request $request, $id)
    {
        $boarding = CageAssignment::with(['pet.owner'])->findOrFail($id);
        $invoice = $this->ensureBoardingInvoice($boarding);
        $invoice->load(['invoiceItems', 'payments']);

        if ($invoice->is_paid) {
            return back()->withErrors(['error' => 'This boarding invoice is already fully paid.']);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,check,mobile_payment,other',
            'amount' => 'nullable|numeric|min:0.01|max:' . $invoice->balance,
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $amount = isset($validated['amount']) && $validated['amount'] !== null
            ? (float) $validated['amount']
            : (float) $invoice->balance;

        Payment::create([
            'invoice_id' => $invoice->id,
            'payment_date' => now(),
            'amount' => $amount,
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'received_by' => auth()->id(),
            'notes' => $validated['notes'] ?? 'Boarding payment for assignment #' . $boarding->id,
        ]);

        $invoice->load(['invoiceItems', 'payments']);
        if ($invoice->balance <= 0) {
            $invoice->update(['status' => 'paid']);
        } else {
            $invoice->update(['status' => 'partial']);
        }

        return redirect()
            ->route('admin.boarding.show', $boarding->id)
            ->with('success', 'Payment recorded successfully for invoice ' . $invoice->invoice_number . '.');
    }

    /**
     * Show the form for editing the specified boarding service.
     */
    public function edit($id)
    {
        $boarding = CageAssignment::with(['pet.owner.user', 'cage'])->findOrFail($id);
        $pets = Pet::with(['owner.user'])->orderBy('name')->get();
        $cages = Cage::where('status', 'available')->orWhere('id', $boarding->cage_id)->orderBy('cage_code')->get();
        
        return view('admin.boarding.edit', compact('boarding', 'pets', 'cages'));
    }

    /**
     * Update the specified boarding service in storage.
     */
    public function update(Request $request, $id)
    {
        $boarding = CageAssignment::findOrFail($id);
        
        $info = $request->validate([
            'cage_id'  => 'required|exists:cages,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'check_in_time'       => 'nullable|date_format:H:i',
            'check_out_time'      => 'nullable|date_format:H:i',
            'feeding_schedule'    => 'nullable|string',
            'feeding_times'       => 'nullable|string',
            'special_diet_notes'  => 'nullable|string',
            'medication_instructions' => 'nullable|string',
            'medication_times'    => 'nullable|string',
            'daily_rate'          => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        // If cage changed, update old cage status and new cage status
        if ($boarding->cage_id != $info['cage_id']) {
            $oldCage = Cage::find($boarding->cage_id);
            if ($oldCage) {
                // Sync old cage status (may become available if no other active assignments)
                $oldCage->syncStatus();
            }

            $newCage = Cage::find($info['cage_id']);
            if ($newCage) {
                $newCage->status = 'occupied';
                $newCage->save();
            }
        }

        // Determine dates: if provided, use new; otherwise keep existing
        $startDate = $info['start_date'] ?? $boarding->start_date;
        $endDate = $info['end_date'] ?? $boarding->end_date;

        // Determine check_in_time: if provided, combine with start_date; otherwise keep existing
        $checkInTime = isset($info['check_in_time']) 
            ? $this->combineDateAndTime($startDate, $info['check_in_time'])
            : $boarding->check_in_time;

        // Determine check_out_time: if provided, combine with end_date; otherwise keep existing
        $checkOutTime = isset($info['check_out_time']) 
            ? $this->combineDateAndTime($endDate, $info['check_out_time'])
            : $boarding->check_out_time;

        // Update cage assignment with all fields directly from form
        $boarding->update([
            'cage_id' => $info['cage_id'],
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'feeding_schedule' => $info['feeding_schedule'] ?? null,
            'feeding_times' => $info['feeding_times'] ?? null,
            'special_diet_notes' => $info['special_diet_notes'] ?? null,
            'medication_instructions' => $info['medication_instructions'] ?? null,
            'medication_times' => $info['medication_times'] ?? null,
            'daily_rate' => $info['daily_rate'] ?? null,
            'notes' => $info['notes'] ?? null,
        ]);
        
        // Sync cage status to ensure accuracy (important if dates changed)
        $cage = Cage::find($info['cage_id']);
        if ($cage) {
            $cage->syncStatus();
        }
        
        return redirect()->route('admin.boarding.index')->with('success', 'Boarding updated successfully.');
    }

    /**
     * Remove the specified boarding service from storage.
     */
    public function destroy($id)
    {
        $boarding = CageAssignment::findOrFail($id);
        
        // Release the cage by syncing its status
        $cage = Cage::find($boarding->cage_id);
        
        $boarding->delete();
        
        // Sync cage status after deletion (it may now be available)
        if ($cage) {
            $cage->syncStatus();
        }

        return redirect()->route('admin.boarding.index')->with('success', 'Boarding deleted successfully.');
    }

    private function combineDateAndTime($date, ?string $time)
    {
        if (!$time) {
            return null;
        }

        // Ensure date is just Y-m-d (take first 10 chars)
        // This handles cases where $date is a full datetime string (e.g. from DB)
        $date = substr((string)$date, 0, 10);

        return \Carbon\Carbon::parse($date . ' ' . $time);
    }

    private function findUnpaidCompletedBoarding(int $petId): ?CageAssignment
    {
        $now = now();

        $completedBoardings = CageAssignment::where('pet_id', $petId)
            ->where(function ($query) use ($now) {
                $query->where(function ($sub) use ($now) {
                    $sub->whereNotNull('check_out_time')
                        ->where('check_out_time', '<=', $now);
                })->orWhere(function ($sub) use ($now) {
                    $sub->whereNull('check_out_time')
                        ->whereDate('end_date', '<', $now->toDateString());
                });
            })
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->get();

        foreach ($completedBoardings as $boarding) {
            if (!$boarding instanceof CageAssignment) {
                continue;
            }

            $invoice = $this->findBoardingInvoice($boarding);
            if (!$invoice || !$invoice->is_paid) {
                return $boarding;
            }
        }

        return null;
    }

    private function findBoardingInvoice(CageAssignment $boarding): ?Invoice
    {
        $tag = $this->boardingInvoiceTag($boarding->id);

        return Invoice::with(['invoiceItems', 'payments'])
            ->where('notes', 'like', '%' . $tag . '%')
            ->orderByDesc('id')
            ->first();
    }

    private function ensureBoardingInvoice(CageAssignment $boarding): Invoice
    {
        $existing = $this->findBoardingInvoice($boarding);
        if ($existing) {
            return $existing;
        }

        $ownerId = optional($boarding->pet)->owner_id;
        if (!$ownerId) {
            abort(422, 'Cannot create boarding invoice because pet owner is missing.');
        }

        $issueDate = now()->toDateString();
        $invoice = new Invoice([
            'owner_id' => $ownerId,
            'pet_id' => $boarding->pet_id,
            'invoice_prefix' => 'INV',
            'issue_date' => $issueDate,
            'due_date' => Carbon::parse($issueDate)->addDays(7)->toDateString(),
            'tax_rate' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
            'notes' => 'Boarding invoice for assignment #' . $boarding->id . ' ' . $this->boardingInvoiceTag($boarding->id),
        ]);

        $invoice->invoice_number = $invoice->generateInvoiceNumber();
        $invoice->save();

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'boarding',
            'description' => $this->boardingInvoiceDescription($boarding),
            'quantity' => 1,
            'unit_price' => $this->calculateBoardingAmount($boarding),
        ]);

        return $invoice->load(['invoiceItems', 'payments']);
    }

    private function calculateBoardingAmount(CageAssignment $boarding): float
    {
        if ($boarding->daily_rate === null) {
            return 0.0;
        }

        $start = Carbon::parse($boarding->start_date);
        $end = Carbon::parse($boarding->end_date);
        $days = max(1, $end->diffInDays($start) + 1);

        return round($days * (float) $boarding->daily_rate, 2);
    }

    private function boardingInvoiceDescription(CageAssignment $boarding): string
    {
        $petName = optional($boarding->pet)->name ?? 'Pet';
        $cageCode = optional($boarding->cage)->cage_code ?? 'N/A';
        $start = $boarding->start_date ? Carbon::parse($boarding->start_date)->format('Y-m-d') : 'N/A';
        $end = $boarding->end_date ? Carbon::parse($boarding->end_date)->format('Y-m-d') : 'N/A';

        return 'Boarding for ' . $petName . ' (Cage ' . $cageCode . ') from ' . $start . ' to ' . $end;
    }

    private function boardingInvoiceTag(int $boardingId): string
    {
        return '[BOARDING_ID:' . $boardingId . ']';
    }

    private function hasBlockingBoardingOverlap(int $petId, string $startDate, string $endDate): bool
    {
        $now = now();
        $today = $now->toDateString();

        return CageAssignment::where('pet_id', $petId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->where(function ($query) use ($now, $today) {
                $query->where(function ($q) use ($today) {
                    $q->whereNull('check_out_time')
                        ->whereDate('end_date', '>=', $today);
                })->orWhere('check_out_time', '>', $now);
            })
            ->exists();
    }
}
