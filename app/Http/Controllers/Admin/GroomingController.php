<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\GroomingAppointment;
use App\Models\GroomingService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GroomingController extends BaseController
{
    /**
     * Display a listing of grooming appointments.
     */
    public function index(Request $request)
    {
        $showTrash = $request->boolean('trash');

        $groomingAppointments = GroomingAppointment::with([
            'appointment.pet.owner.user',
            'service',
            'groomer'
        ])
            ->when($showTrash, function ($query) {
                $query->onlyTrashed();
            })
            ->orderByDesc('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        $linkedAppointmentIds = $groomingAppointments
            ->pluck('appointment_id')
            ->filter()
            ->all();

        $appointmentGroomings = collect();
        if (!$showTrash) {
            $appointmentGroomings = Appointment::with(['pet.owner.user', 'veterinarian'])
                ->where('type', 'grooming')
                ->whereIn('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])
                ->when(!empty($linkedAppointmentIds), function ($query) use ($linkedAppointmentIds) {
                    $query->whereNotIn('id', $linkedAppointmentIds);
                })
                ->orderByDesc('appointment_date')
                ->get();
        }

        $virtualGroomings = $appointmentGroomings->map(function ($appointment) {
            $groomingAppointment = new GroomingAppointment();
            $groomingAppointment->setRelation('appointment', $appointment);
            $groomingAppointment->setAttribute('status', $this->mapGroomingStatusFromAppointment($appointment->status));
            $groomingAppointment->setAttribute('is_virtual', true);
            return $groomingAppointment;
        });

        $groomingAppointments = $groomingAppointments
            ->concat($virtualGroomings)
            ->sortByDesc(function ($item) {
                $date = optional($item->appointment)->appointment_date;
                return $date ? $date->timestamp : 0;
            })
            ->values();

        $appointmentIds = $groomingAppointments
            ->map(fn ($item) => optional($item->appointment)->id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $invoicesByAppointment = Invoice::with(['invoiceItems', 'payments'])
            ->whereIn('appointment_id', $appointmentIds)
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('issue_date')
            ->get()
            ->groupBy('appointment_id');

        $groomingAppointments = $groomingAppointments->map(function ($item) use ($invoicesByAppointment) {
            $appointmentId = optional($item->appointment)->id;
            $invoice = $appointmentId
                ? optional($invoicesByAppointment->get($appointmentId))->first()
                : null;

            if (!$invoice) {
                $item->setAttribute('payment_status', 'unbilled');
                $item->setAttribute('invoice_id', null);
                $item->setAttribute('invoice_total', null);
                $item->setAttribute('invoice_tax', null);
                return $item;
            }

            $paymentStatus = $invoice->is_paid
                ? 'paid'
                : ((float) $invoice->paid_amount > 0 ? 'partial' : 'unpaid');

            $item->setAttribute('payment_status', $paymentStatus);
            $item->setAttribute('invoice_id', $invoice->id);
            $item->setAttribute('invoice_total', (float) $invoice->total_amount);
            $item->setAttribute('invoice_tax', (float) $invoice->tax_amount);

            return $item;
        });

        $todayAppointments = Appointment::where('type', 'grooming')
            ->whereDate('appointment_date', Carbon::today())
            ->count();

        $completedAppointments = Appointment::where('type', 'grooming')
            ->where('status', 'completed')
            ->count();
        $servicesCount = GroomingService::count();

        return view('admin.grooming.index', compact('groomingAppointments', 'todayAppointments', 'completedAppointments', 'servicesCount'));
    }

    /**
     * Show the form for creating a new grooming appointment.
     */
    public function create()
    {
        $pets = Pet::with(['owner.user'])
            ->orderBy('name')
            ->get();

        $services = GroomingService::orderBy('service_name')->get();
        $groomers = User::where('role', 'groomer')->orderBy('first_name')->get();

        return view('admin.grooming.create', compact('pets', 'services', 'groomers'));
    }

    /**
     * Show form to complete grooming details for an existing appointment.
     */
    public function completeFromAppointment($appointmentId)
    {
        $appointment = Appointment::with(['pet.owner.user'])->findOrFail($appointmentId);

        if ($appointment->type !== 'grooming') {
            return redirect()->route('admin.grooming.index')
                ->with('error', 'Only grooming appointments can be completed here.');
        }

        $existing = GroomingAppointment::where('appointment_id', $appointment->id)->first();
        if ($existing) {
            return redirect()->route('admin.grooming.edit', $existing->id)
                ->with('info', 'Grooming details already exist. You can edit them below.');
        }

        $services = GroomingService::orderBy('service_name')->get();
        $groomers = User::where('role', 'groomer')->orderBy('first_name')->get();

        return view('admin.grooming.complete', compact('appointment', 'services', 'groomers'));
    }

    /**
     * Store grooming details for an existing grooming appointment record.
     */
    public function storeFromAppointment(Request $request, $appointmentId, NotificationService $notificationService)
    {
        $appointment = Appointment::findOrFail($appointmentId);

        if ($appointment->type !== 'grooming') {
            return redirect()->route('admin.grooming.index')
                ->with('error', 'Only grooming appointments can be completed here.');
        }

        $existing = GroomingAppointment::where('appointment_id', $appointment->id)->first();
        if ($existing) {
            return redirect()->route('admin.grooming.edit', $existing->id)
                ->with('info', 'Grooming details already exist.');
        }

        $data = $request->validate([
            'service_id' => 'required|exists:grooming_services,id',
            'groomer_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $appointment->update([
            'veterinarian_id' => $data['groomer_id'] ?? $appointment->veterinarian_id,
            'appointment_date' => $data['appointment_date'],
            'status' => $this->mapAppointmentStatus($data['status']),
            'notes' => $data['notes'] ?? $appointment->notes,
        ]);

        $groomingAppointment = GroomingAppointment::create([
            'appointment_id' => $appointment->id,
            'service_id' => $data['service_id'],
            'groomer_id' => $data['groomer_id'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
            'status' => $data['status'],
        ]);

        $appointment->loadMissing('pet');
        $petName = $appointment->pet?->name ?? 'Pet';
        $message = 'Grooming appointment scheduled for ' . $petName . '.';

        if (!empty($data['groomer_id'])) {
            $groomer = User::find($data['groomer_id']);
            if ($groomer) {
                $notificationService->send(
                    $groomer,
                    Notification::TYPE_APPOINTMENT,
                    'New Grooming Appointment',
                    $message,
                    [
                        'reference_type' => 'appointment',
                        'reference_id' => $appointment->id,
                        'action_url' => route('admin.grooming.show', $groomingAppointment->id),
                    ]
                );
            }
        } else {
            $notificationService->sendToRole(
                'groomer',
                Notification::TYPE_APPOINTMENT,
                'New Grooming Appointment',
                $message,
                [
                    'reference_type' => 'appointment',
                    'reference_id' => $appointment->id,
                    'action_url' => route('admin.grooming.show', $groomingAppointment->id),
                ]
            );
        }

        $notificationService->sendToRole(
            'staff',
            Notification::TYPE_APPOINTMENT,
            'New Grooming Appointment',
            $message,
            [
                'reference_type' => 'appointment',
                'reference_id' => $appointment->id,
                'action_url' => route('admin.grooming.show', $groomingAppointment->id),
            ]
        );

        if ($data['status'] === 'completed') {
            $this->ensureGroomingInvoice($groomingAppointment);
        }

        return redirect()->route('admin.grooming.show', $groomingAppointment->id)
            ->with('success', 'Grooming details completed successfully.');
    }

    /**
     * Store a newly created grooming appointment.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:grooming_services,id',
            'groomer_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create([
            'pet_id' => $data['pet_id'],
            'veterinarian_id' => $data['groomer_id'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'status' => 'confirmed',
            'type' => 'grooming',
            'notes' => $data['notes'] ?? null,
        ]);

        GroomingAppointment::create([
            'appointment_id' => $appointment->id,
            'service_id' => $data['service_id'],
            'groomer_id' => $data['groomer_id'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
            'status' => 'scheduled',
        ]);

        $appointment->loadMissing('pet');
        $petName = $appointment->pet?->name ?? 'Pet';
        $message = 'Grooming appointment scheduled for ' . $petName . '.';

        if (!empty($data['groomer_id'])) {
            $groomer = User::find($data['groomer_id']);
            if ($groomer) {
                $notificationService->send(
                    $groomer,
                    Notification::TYPE_APPOINTMENT,
                    'New Grooming Appointment',
                    $message,
                    [
                        'reference_type' => 'appointment',
                        'reference_id' => $appointment->id,
                        'action_url' => route('admin.grooming.index'),
                    ]
                );
            }
        } else {
            $notificationService->sendToRole(
                'groomer',
                Notification::TYPE_APPOINTMENT,
                'New Grooming Appointment',
                $message,
                [
                    'reference_type' => 'appointment',
                    'reference_id' => $appointment->id,
                    'action_url' => route('admin.grooming.index'),
                ]
            );
        }

        $notificationService->sendToRole(
            'staff',
            Notification::TYPE_APPOINTMENT,
            'New Grooming Appointment',
            $message,
            [
                'reference_type' => 'appointment',
                'reference_id' => $appointment->id,
                'action_url' => route('admin.grooming.index'),
            ]
        );

        return redirect()->route('admin.grooming.index')
            ->with('success', 'Grooming appointment created successfully.');
    }

    /**
     * Display the specified grooming appointment.
     */
    public function show($id)
    {
        $groomingAppointment = GroomingAppointment::with([
            'appointment.pet.owner.user',
            'appointment.veterinarian',
            'service',
            'groomer'
        ])->findOrFail($id);

        return view('admin.grooming.show', compact('groomingAppointment'));
    }

    /**
     * Show the form for editing the specified grooming appointment.
     */
    public function edit($id)
    {
        $groomingAppointment = GroomingAppointment::with([
            'appointment.pet.owner.user',
            'service',
            'groomer'
        ])->findOrFail($id);

        $services = GroomingService::orderBy('service_name')->get();
        $groomers = User::where('role', 'groomer')->orderBy('first_name')->get();

        return view('admin.grooming.edit', compact('groomingAppointment', 'services', 'groomers'));
    }

    /**
     * Update the specified grooming appointment.
     */
    public function update(Request $request, NotificationService $notificationService, $id)
    {
        $groomingAppointment = GroomingAppointment::findOrFail($id);
        $appointment = $groomingAppointment->appointment;
        $previousGroomerId = $groomingAppointment->groomer_id;

        $data = $request->validate([
            'service_id' => 'required|exists:grooming_services,id',
            'groomer_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $appointment->update([
                'veterinarian_id' => $data['groomer_id'] ?? null,
                'appointment_date' => $data['appointment_date'],
                'status' => $this->mapAppointmentStatus($data['status']),
                'notes' => $data['notes'] ?? null,
            ]);

            $groomingAppointment->update([
                'service_id' => $data['service_id'],
                'groomer_id' => $data['groomer_id'] ?? null,
                'special_instructions' => $data['special_instructions'] ?? null,
                'status' => $data['status'],
            ]);

            $newGroomerId = $data['groomer_id'] ?? null;
            $shouldNotifyAssignedGroomer = !empty($newGroomerId) && (int) $newGroomerId !== (int) $previousGroomerId;

            if ($data['status'] === 'completed') {
                $this->ensureGroomingInvoice($groomingAppointment);
            }

            if ($data['status'] === 'cancelled') {
                $this->cancelGroomingInvoice($groomingAppointment);
            }

            DB::commit();

            if ($shouldNotifyAssignedGroomer) {
                $assignedGroomer = User::find($newGroomerId);

                if ($assignedGroomer) {
                    $appointment->loadMissing('pet');
                    $petName = $appointment->pet?->name ?? 'Pet';

                    $notificationService->send(
                        $assignedGroomer,
                        Notification::TYPE_APPOINTMENT,
                        'Grooming Assignment Updated',
                        'You have been assigned to a grooming appointment for ' . $petName . '.',
                        [
                            'reference_type' => 'appointment',
                            'reference_id' => $appointment->id,
                            'action_url' => route('admin.grooming.show', $groomingAppointment->id),
                        ]
                    );
                }
            }

            return redirect()->route('admin.grooming.show', $groomingAppointment->id)
                ->with('success', 'Grooming appointment updated successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Unable to update grooming appointment: ' . $exception->getMessage()]);
        }
    }

    /**
     * Remove the specified grooming appointment.
     */
    public function destroy($id)
    {
        $groomingAppointment = GroomingAppointment::findOrFail($id);
        $appointment = $groomingAppointment->appointment;
        
        $groomingAppointment->delete();
        $appointment->delete();

        return redirect()->route('admin.grooming.index')
            ->with('success', 'Grooming appointment deleted successfully.');
    }

    public function restore(int $id)
    {
        $groomingAppointment = GroomingAppointment::onlyTrashed()->withTrashed()->findOrFail($id);
        $groomingAppointment->restore();

        $appointment = Appointment::withTrashed()->find($groomingAppointment->appointment_id);
        if ($appointment && method_exists($appointment, 'trashed') && $appointment->trashed()) {
            $appointment->restore();
        }

        return redirect()->back()->with('success', 'Grooming appointment restored successfully.');
    }

    public function markPaid($id)
    {
        $groomingAppointment = GroomingAppointment::with(['appointment.pet', 'service'])->findOrFail($id);
        $appointment = $groomingAppointment->appointment;

        if (!$appointment) {
            return back()->withErrors(['error' => 'Linked appointment not found.']);
        }

        DB::beginTransaction();

        try {
            $invoice = $this->ensureGroomingInvoice($groomingAppointment);

            $invoice->load(['invoiceItems', 'payments']);

            if ($invoice->is_paid) {
                DB::commit();
                return back()->with('success', 'Grooming invoice is already paid.');
            }

            $balance = (float) $invoice->balance;
            if ($balance <= 0) {
                $invoice->update(['status' => 'paid']);
                DB::commit();
                return back()->with('success', 'Grooming invoice marked as paid.');
            }

            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => now(),
                'amount' => $balance,
                'payment_method' => 'cash',
                'reference_number' => null,
                'received_by' => auth()->id(),
                'notes' => 'Paid from grooming appointments list.',
            ]);

            $invoice->load(['invoiceItems', 'payments']);
            $invoice->update(['status' => $invoice->balance <= 0 ? 'paid' : 'partial']);

            DB::commit();

            return back()->with('success', 'Grooming payment recorded successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Unable to mark grooming as paid: ' . $exception->getMessage()]);
        }
    }

    private function ensureGroomingInvoice(GroomingAppointment $groomingAppointment): Invoice
    {
        $groomingAppointment->loadMissing(['appointment.pet', 'service']);
        $appointment = $groomingAppointment->appointment;

        $invoice = Invoice::with(['invoiceItems', 'payments'])
            ->where('appointment_id', optional($appointment)->id)
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('issue_date')
            ->first();

        if ($invoice instanceof Invoice) {
            return $this->syncGroomingInvoice($groomingAppointment, $invoice);
        }

        $ownerId = optional($appointment->pet)->owner_id;
        if (!$ownerId) {
            throw new \RuntimeException('Cannot create invoice: pet owner is missing.');
        }

        $prefix = ClinicSetting::invoicePrefix();
        $defaultTaxRate = ClinicSetting::defaultTaxRate();
        $year = now()->format('Y');
        $lastSequence = Invoice::where('invoice_prefix', $prefix)
            ->whereYear('issue_date', $year)
            ->max('invoice_sequence');
        $nextSequence = $lastSequence ? ((int) $lastSequence + 1) : 1;

        $invoice = Invoice::create([
            'invoice_number' => sprintf('%s-%s-%06d', $prefix, $year, $nextSequence),
            'appointment_id' => $appointment->id,
            'pet_id' => $appointment->pet_id,
            'owner_id' => $ownerId,
            'invoice_prefix' => $prefix,
            'invoice_sequence' => $nextSequence,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'status' => 'pending',
            'tax_rate' => $defaultTaxRate,
            'discount_amount' => 0,
            'notes' => 'Invoice for grooming appointment on ' . optional($appointment->appointment_date)->format('M d, Y'),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'grooming',
            'description' => $groomingAppointment->service->service_name ?? 'Grooming Service',
            'quantity' => 1,
            'unit_price' => (float) ($groomingAppointment->service->price ?? 0),
        ]);

        return $this->syncGroomingInvoice($groomingAppointment, $invoice->load(['invoiceItems', 'payments']));
    }

    private function syncGroomingInvoice(GroomingAppointment $groomingAppointment, Invoice $invoice): Invoice
    {
        $groomingAppointment->loadMissing(['appointment', 'service']);
        $invoice->loadMissing(['invoiceItems', 'payments']);

        $unitPrice = round((float) ($groomingAppointment->service->price ?? 0), 2);
        $description = $groomingAppointment->service->service_name ?? 'Grooming Service';

        $item = $invoice->invoiceItems->firstWhere('item_type', 'grooming')
            ?? $invoice->invoiceItems->first();

        if ($item) {
            $updates = [];

            if ((string) $item->item_type !== 'grooming') {
                $updates['item_type'] = 'grooming';
            }

            if ((int) $item->quantity !== 1) {
                $updates['quantity'] = 1;
            }

            if ((float) $item->unit_price !== $unitPrice) {
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
                'item_type' => 'grooming',
                'description' => $description,
                'quantity' => 1,
                'unit_price' => $unitPrice,
            ]);
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

    private function cancelGroomingInvoice(GroomingAppointment $groomingAppointment): void
    {
        $groomingAppointment->loadMissing('appointment');
        $appointmentId = optional($groomingAppointment->appointment)->id;

        if (!$appointmentId) {
            return;
        }

        $invoice = Invoice::with(['payments'])
            ->where('appointment_id', $appointmentId)
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('issue_date')
            ->first();

        if (!$invoice instanceof Invoice) {
            return;
        }

        $invoice->payments()->delete();
        $invoice->update(['status' => 'cancelled']);
    }

    private function mapAppointmentStatus(string $groomingStatus): string
    {
        return match ($groomingStatus) {
            'scheduled' => 'confirmed',
            'in_progress' => 'in_progress',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => 'pending',
        };
    }

    private function mapGroomingStatusFromAppointment(string $appointmentStatus): string
    {
        return match ($appointmentStatus) {
            'pending', 'confirmed' => 'scheduled',
            'in_progress' => 'in_progress',
            'completed' => 'completed',
            'cancelled', 'no_show' => 'cancelled',
            default => 'scheduled',
        };
    }

    /**
     * Display a listing of grooming services.
     */
    public function servicesIndex()
    {
        $services = GroomingService::orderBy('service_name')->get();
        $totalServices = $services->count();
        
        return view('admin.grooming.services.index', compact('services', 'totalServices'));
    }

    /**
     * Show the form for creating a new grooming service.
     */
    public function servicesCreate()
    {
        return view('admin.grooming.services.create');
    }

    /**
     * Store a newly created grooming service.
     */
    public function servicesStore(Request $request)
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        GroomingService::create($data);

        return redirect()->route('admin.grooming-services.index')
            ->with('success', 'Grooming service created successfully.');
    }

    /**
     * Display the specified grooming service.
     */
    public function servicesShow($id)
    {
        $service = GroomingService::findOrFail($id);
        
        return view('admin.grooming.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified grooming service.
     */
    public function servicesEdit($id)
    {
        $service = GroomingService::findOrFail($id);
        
        return view('admin.grooming.services.edit', compact('service'));
    }

    /**
     * Update the specified grooming service.
     */
    public function servicesUpdate(Request $request, $id)
    {
        $service = GroomingService::findOrFail($id);

        $data = $request->validate([
            'service_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $service->update($data);

        return redirect()->route('admin.grooming-services.show', $service->id)
            ->with('success', 'Grooming service updated successfully.');
    }

    /**
     * Remove the specified grooming service.
     */
    public function servicesDestroy($id)
    {
        $service = GroomingService::findOrFail($id);
        
        if ($service->groomingAppointments()->count() > 0) {
            return redirect()->route('admin.grooming-services.index')
                ->with('error', 'Cannot delete service that has appointments.');
        }
        
        $service->delete();

        return redirect()->route('admin.grooming-services.index')
            ->with('success', 'Grooming service deleted successfully.');
    }
}
