<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Pet;
use App\Models\Surgery;
use App\Models\SurgeryType;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\StaffSchedule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SurgeryController extends BaseController
{
    /**
     * Display a listing of surgeries.
     */
    public function index(Request $request)
    {
        $showTrash = $request->boolean('trash');

        $pets = Pet::with([
            'owner',
            'surgeries' => function ($query) use ($showTrash) {
                if ($showTrash) {
                    $query->onlyTrashed();
                }
            },
            'appointments' => function ($query) {
                $query->where('type', 'surgery');
            },
        ])
            ->where(function ($query) use ($showTrash) {
                if ($showTrash) {
                    $query->whereHas('surgeries', function ($surgeryQuery) {
                        $surgeryQuery->onlyTrashed();
                    });
                } else {
                    $query->has('surgeries')
                        ->orWhereHas('appointments', function ($appointmentQuery) {
                            $appointmentQuery->where('type', 'surgery');
                        });
                }
            })
            ->paginate(10);

        $pets->getCollection()->transform(function ($pet) use ($showTrash) {
            $appointmentCount = $showTrash ? 0 : ($pet->appointments ? $pet->appointments->count() : 0);
            $pet->surgery_appointment_count = $appointmentCount;
            $pet->surgery_total_count = $pet->surgeries->count() + $appointmentCount;
            return $pet;
        });

        return view('admin.surgeries.index', compact('pets'));
    }

    /**
     * Show the form for creating a new surgery.
     */
    public function create()
    {
        $pets = Pet::with('owner.user')->get();
        
        $selectedDate = request()->query('scheduled_date', date('Y-m-d'));
        $dayOfWeek = \Carbon\Carbon::parse($selectedDate)->format('l');
        
        // Get surgeons scheduled for the selected date
        $scheduledStaffIds = StaffSchedule::where('is_active', true)
            ->where('day_of_week', $dayOfWeek)
            ->pluck('user_id')
            ->unique()
            ->toArray();
        
        $surgeons = User::where('role', 'veterinarian')
            ->where('is_active', true)
            ->whereIn('id', $scheduledStaffIds)
            ->orderBy('first_name')
            ->get();
        
        $surgeryTypes = SurgeryType::where('is_active', true)->orderBy('name')->get();
        $medicalRecords = MedicalRecord::with('pet')->get();

        return view('admin.surgeries.create', compact('pets', 'surgeons', 'surgeryTypes', 'medicalRecords'));
    }

    /**
     * Store a newly created surgery in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'surgeon_id' => 'required|exists:users,id',
            'surgery_type_id' => 'required|exists:surgery_types,id',
            'scheduled_date' => 'required|date_format:Y-m-d\TH:i',
            'anesthesia_type' => 'nullable|string|max:255',
            'pre_op_notes' => 'nullable|string',
            'post_op_instructions' => 'nullable|string',
            'medical_record_id' => 'nullable|exists:medical_records,id',
        ]);

        $surgery = DB::transaction(function () use ($validated) {
            $surgery = Surgery::create([
                'pet_id' => $validated['pet_id'],
                'surgeon_id' => $validated['surgeon_id'],
                'surgery_type_id' => $validated['surgery_type_id'],
                'scheduled_date' => $validated['scheduled_date'],
                'anesthesia_type' => $validated['anesthesia_type'],
                'pre_op_notes' => $validated['pre_op_notes'],
                'post_op_instructions' => $validated['post_op_instructions'],
                'medical_record_id' => $validated['medical_record_id'],
                'status' => 'scheduled',
            ]);

            $this->ensureSurgeryInvoice($surgery);

            return $surgery;
        });

        return redirect()->route('admin.surgeries.index')
            ->with('success', 'Surgery scheduled successfully!');
    }

    /**
     * Display the specified surgery.
     */
    public function show($id)
    {
        $surgery = Surgery::with(['pet.owner.user', 'surgeon', 'medicalRecord', 'surgeryType'])
            ->findOrFail($id);

        return view('admin.surgeries.show', compact('surgery'));
    }

    /**
     * Show the form for editing the specified surgery.
     */
    public function edit($id)
    {
        $surgery = Surgery::findOrFail($id);
        $pets = Pet::with('owner.user')->get();
        
        // Get surgeons scheduled for the surgery date
        $dayOfWeek = \Carbon\Carbon::parse($surgery->scheduled_date)->format('l');
        $scheduledStaffIds = StaffSchedule::where('is_active', true)
            ->where('day_of_week', $dayOfWeek)
            ->pluck('user_id')
            ->unique()
            ->toArray();
        
        $surgeons = User::where('role', 'veterinarian')
            ->where('is_active', true)
            ->whereIn('id', $scheduledStaffIds)
            ->orderBy('first_name')
            ->get();
        
        $surgeryTypes = SurgeryType::where('is_active', true)->orderBy('name')->get();

        return view('admin.surgeries.edit', compact('surgery', 'pets', 'surgeons', 'surgeryTypes'));
    }

    /**
     * Get available surgeons for a specific date
     */
    public function getAvailableSurgeons(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        
        try {
            $dayOfWeek = \Carbon\Carbon::parse($date)->format('l');
        } catch (\Exception $e) {
            return response()->json(['surgeons' => []], 400);
        }
        
        // Get surgeons scheduled for the selected date
        $scheduledStaffIds = StaffSchedule::where('is_active', true)
            ->where('day_of_week', $dayOfWeek)
            ->pluck('user_id')
            ->unique()
            ->toArray();
        
        $surgeons = User::where('role', 'veterinarian')
            ->where('is_active', true)
            ->whereIn('id', $scheduledStaffIds)
            ->orderBy('first_name')
            ->get()
            ->map(function ($surgeon) {
                return [
                    'id' => $surgeon->id,
                    'name' => 'Dr. ' . $surgeon->first_name . ' ' . $surgeon->last_name,
                ];
            });
        
        return response()->json(['surgeons' => $surgeons]);
    }

    /**
     * Update the specified surgery in storage.
     */
    public function update(Request $request, $id)
    {
        $surgery = Surgery::findOrFail($id);
        $previousStatus = (string) $surgery->status;

        $validated = $request->validate([
            'surgeon_id' => 'required|exists:users,id',
            'surgery_type_id' => 'required|exists:surgery_types,id',
            'scheduled_date' => 'required|date_format:Y-m-d\TH:i',
            'anesthesia_type' => 'nullable|string|max:255',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
            'pre_op_notes' => 'nullable|string',
            'surgery_notes' => 'nullable|string',
            'post_op_instructions' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        DB::transaction(function () use ($surgery, $validated, $previousStatus) {
            $surgery->update($validated);

            $newStatus = (string) ($validated['status'] ?? $surgery->status);

            if ($newStatus === 'cancelled') {
                $this->cancelSurgeryInvoice($surgery);
                return;
            }

            $invoice = $this->ensureSurgeryInvoice($surgery);

            if ($previousStatus === 'cancelled' && $invoice->status === 'cancelled') {
                $invoice->update(['status' => 'pending']);
            }
        });

        return redirect()->route('admin.surgeries.show', $surgery->id)
            ->with('success', 'Surgery updated successfully!');
    }

    /**
     * Remove the specified surgery from storage.
     */
    public function destroy($id)
    {
        $surgery = Surgery::findOrFail($id);
        $surgery->delete();

        return redirect()->route('admin.surgeries.index')
            ->with('success', 'Surgery deleted successfully!');
    }

    /**
     * Display surgeries for a specific pet.
     */
    public function byPet($petId, Request $request)
    {
        $showTrash = $request->boolean('trash');

        $pet = Pet::with(['surgeries.surgeon', 'surgeries.surgeryType'])->findOrFail($petId);
        $surgeries = $pet->surgeries()
            ->when($showTrash, function ($query) {
                $query->onlyTrashed();
            })
            ->with(['surgeon', 'surgeryType'])
            ->orderByDesc('deleted_at')
            ->orderBy('scheduled_date', 'desc')
            ->get();

        $appointmentSurgeries = collect();
        if (!$showTrash) {
            $appointmentSurgeries = $pet->appointments()
                ->where('type', 'surgery')
                ->orderBy('appointment_date', 'desc')
                ->get();
        }

        $virtualSurgeries = $appointmentSurgeries->map(function ($appointment) {
            $surgery = new Surgery();
            $surgery->setRelation('pet', $appointment->pet);
            $surgery->setRelation('appointment', $appointment);
            $surgery->setAttribute('scheduled_date', $appointment->appointment_date);
            $surgery->setAttribute('status', $this->mapSurgeryStatusFromAppointment($appointment->status));
            $surgery->setAttribute('is_virtual', true);
            return $surgery;
        });

        $allSurgeries = $surgeries
            ->concat($virtualSurgeries)
            ->sortByDesc(function ($item) {
                $date = $item->scheduled_date ?? optional($item->appointment)->appointment_date;
                return $date ? $date->timestamp : 0;
            })
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pageItems = $allSurgeries->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Load invoices for non-virtual surgeries
        $pageItems = $pageItems->map(function ($item) {
            $isVirtual = (bool) $item->getAttribute('is_virtual');
            if ($isVirtual) {
                return $item;
            }

            $invoice = $this->findSurgeryInvoice($item);
            if ($invoice) {
                $item->setAttribute('billing_invoice', $invoice);
            }

            return $item;
        });

        $surgeries = new LengthAwarePaginator(
            $pageItems,
            $allSurgeries->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.surgeries.pet', compact('pet', 'surgeries'));
    }

    public function restore(int $id)
    {
        $surgery = Surgery::onlyTrashed()->findOrFail($id);
        $surgery->restore();

        return redirect()->back()->with('success', 'Surgery record restored successfully.');
    }

    /**
     * Process payment for surgery.
     */
    public function processPayment($id)
    {
        $surgery = Surgery::with(['pet.owner', 'surgeryType'])->findOrFail($id);
        $invoice = $this->ensureSurgeryInvoice($surgery);
        $invoice->load(['invoiceItems', 'payments']);

        if ($invoice->status === 'cancelled') {
            return back()->withErrors(['error' => 'Cannot process payment for a cancelled invoice.']);
        }

        if ($invoice->is_paid) {
            return back()->with('success', 'Invoice is already paid.');
        }

        $balance = $invoice->balance;
        if ($balance <= 0) {
            $invoice->update(['status' => 'paid']);
            return back()->with('success', 'Surgery invoice marked as paid.');
        }

        DB::beginTransaction();
        try {
            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => now(),
                'amount' => $balance,
                'payment_method' => 'cash',
                'reference_number' => null,
                'received_by' => Auth::id(),
                'notes' => 'Paid from surgery records list.',
            ]);

            $invoice->load(['invoiceItems', 'payments']);
            $invoice->update(['status' => $invoice->balance <= 0 ? 'paid' : 'partial']);

            DB::commit();

            return back()->with('success', 'Surgery payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Unable to mark surgery as paid: ' . $e->getMessage()]);
        }
    }

    // ===== Private Helper Methods =====

    private function ensureSurgeryInvoice(Surgery $surgery): Invoice
    {
        $existing = $this->findSurgeryInvoice($surgery);
        if ($existing) {
            return $this->syncSurgeryInvoice($surgery, $existing);
        }

        $surgery->loadMissing(['pet.owner']);
        $ownerId = optional($surgery->pet)->owner_id;

        if (!$ownerId) {
            abort(422, 'Cannot create surgery invoice because pet owner is missing.');
        }

        $issueDate = $surgery->scheduled_date
            ? Carbon::parse($surgery->scheduled_date)->toDateString()
            : now()->toDateString();
        $prefix = ClinicSetting::invoicePrefix();
        $defaultTaxRate = ClinicSetting::defaultTaxRate();

        $invoice = new Invoice([
            'owner_id' => $ownerId,
            'pet_id' => $surgery->pet_id,
            'invoice_prefix' => $prefix,
            'issue_date' => $issueDate,
            'due_date' => $issueDate,
            'tax_rate' => $defaultTaxRate,
            'discount_amount' => 0,
            'status' => 'pending',
            'notes' => 'Surgery invoice for record #' . $surgery->id . ' ' . $this->surgeryInvoiceTag($surgery->id),
        ]);

        $invoice->invoice_number = $invoice->generateInvoiceNumber();
        $invoice->save();

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'surgery',
            'description' => $this->surgeryInvoiceDescription($surgery),
            'quantity' => 1,
            'unit_price' => $this->surgeryUnitPrice($surgery),
        ]);

        return $this->syncSurgeryInvoice($surgery, $invoice->load(['invoiceItems', 'payments']));
    }

    private function findSurgeryInvoice(Surgery $surgery): ?Invoice
    {
        $tag = $this->surgeryInvoiceTag($surgery->id);

        $invoice = Invoice::with(['invoiceItems', 'payments'])
            ->where('pet_id', $surgery->pet_id)
            ->where('notes', 'like', '%' . $tag . '%')
            ->orderByDesc('id')
            ->first();

        if (!$invoice instanceof Invoice) {
            return null;
        }

        return $invoice;
    }

    private function syncSurgeryInvoice(Surgery $surgery, Invoice $invoice): Invoice
    {
        $invoice->loadMissing(['invoiceItems', 'payments']);

        $description = $this->surgeryInvoiceDescription($surgery);
        $unitPrice = $this->surgeryUnitPrice($surgery);
        $issueDate = $surgery->scheduled_date
            ? Carbon::parse($surgery->scheduled_date)->toDateString()
            : now()->toDateString();

        $item = $invoice->invoiceItems->firstWhere('item_type', 'surgery')
            ?? $invoice->invoiceItems->first();

        if ($item) {
            $updates = [];

            if ((string) $item->item_type !== 'surgery') {
                $updates['item_type'] = 'surgery';
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
                'item_type' => 'surgery',
                'description' => $description,
                'quantity' => 1,
                'unit_price' => $unitPrice,
            ]);
        }

        $invoiceUpdates = [];

        $currentIssueDate = $invoice->issue_date ? Carbon::parse($invoice->issue_date)->toDateString() : null;
        $currentDueDate = $invoice->due_date ? Carbon::parse($invoice->due_date)->toDateString() : null;

        if ($currentIssueDate !== $issueDate) {
            $invoiceUpdates['issue_date'] = $issueDate;
        }

        if ($currentDueDate !== $issueDate) {
            $invoiceUpdates['due_date'] = $issueDate;
        }

        if (!empty($invoiceUpdates)) {
            $invoice->update($invoiceUpdates);
            $invoice->refresh();
        }

        $invoice->loadMissing(['invoiceItems', 'payments']);

        if ($invoice->status !== 'cancelled') {
            $targetStatus = ($invoice->is_paid ? 'paid'
                : ($invoice->paid_amount > 0 ? 'partial' : 'pending'));

            if ($invoice->status !== $targetStatus) {
                $invoice->update(['status' => $targetStatus]);
                $invoice->refresh();
            }
        }

        return $invoice->load(['invoiceItems', 'payments']);
    }

    private function cancelSurgeryInvoice(Surgery $surgery): void
    {
        $invoice = $this->findSurgeryInvoice($surgery);

        if (!$invoice || $invoice->status === 'cancelled') {
            return;
        }

        $invoice->payments()->delete();

        $invoice->update([
            'status' => 'cancelled',
            'notes' => trim((string) ($invoice->notes ?? '')) . "\n\nCancelled due to surgery cancellation on " . now()->format('Y-m-d H:i:s'),
        ]);
    }

    private function surgeryUnitPrice(Surgery $surgery): float
    {
        $surgery->loadMissing('surgeryType');
        return round((float) (optional($surgery->surgeryType)->price ?? 0), 2);
    }

    private function surgeryInvoiceDescription(Surgery $surgery): string
    {
        $surgery->loadMissing(['pet', 'surgeryType']);
        $petName = optional($surgery->pet)->name ?? 'Pet';
        $surgeryName = optional($surgery->surgeryType)->name ?? 'Surgery';
        $date = $surgery->scheduled_date
            ? Carbon::parse($surgery->scheduled_date)->format('Y-m-d')
            : now()->format('Y-m-d');

        return 'Surgery for ' . $petName . ' (' . $surgeryName . ') on ' . $date;
    }

    private function surgeryInvoiceTag(int $surgeryId): string
    {
        return '[SURGERY_ID:' . $surgeryId . ']';
    }

    private function mapSurgeryStatusFromAppointment(string $appointmentStatus): string
    {
        return match ($appointmentStatus) {
            'in_progress' => 'in_progress',
            'completed' => 'completed',
            'cancelled', 'no_show' => 'cancelled',
            'pending', 'confirmed' => 'scheduled',
            default => 'scheduled',
        };
    }
}
