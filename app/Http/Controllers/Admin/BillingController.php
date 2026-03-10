<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\BillingInvoice;
use App\Models\BillingInvoiceItem;
use App\Models\BillingPayment;
use App\Models\ClinicSetting;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class BillingController extends BaseController
{
    /**
     * Display a listing of billing records.
     */
    public function index(Request $request)
    {
        $showTrash = $request->boolean('trash');

        $invoicesQuery = BillingInvoice::with(['pet', 'petOwner', 'invoiceItems', 'payments']);

        if ($showTrash) {
            $invoicesQuery->onlyTrashed();
        } else {
            $invoicesQuery->where('status', '!=', 'cancelled');
        }

        $invoices = $invoicesQuery
            ->orderByDesc('issue_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);

        $statsQuery = BillingInvoice::query();
        if ($showTrash) {
            $statsQuery->onlyTrashed();
        } else {
            $statsQuery->where('status', '!=', 'cancelled');
        }

        $totalInvoices = (clone $statsQuery)->count();
        $paidInvoices = (clone $statsQuery)->where('status', 'paid')->count();
        $overdueInvoices = (clone $statsQuery)->where('status', 'overdue')->count();
        $totalRevenue = (clone $statsQuery)->with('invoiceItems')
            ->get()
            ->sum(function ($invoice) {
                return (float) $invoice->total_amount;
            });

        $paidAmount = (clone $statsQuery)->with(['invoiceItems', 'payments'])
            ->get()
            ->sum(function ($invoice) {
                $invoiceTotal = (float) $invoice->total_amount;
                $invoicePaid = (float) $invoice->payments->sum('amount');

                return min($invoicePaid, $invoiceTotal);
            });
        
        return view('admin.billing.index', compact(
            'invoices',
            'totalInvoices',
            'paidInvoices',
            'overdueInvoices',
            'totalRevenue',
            'paidAmount',
            'showTrash'
        ));
    }

    public function restore(int $id)
    {
        $invoice = BillingInvoice::onlyTrashed()->findOrFail($id);
        $invoice->restore();

        return redirect()->route('admin.billing.index', ['trash' => 1])
            ->with('success', 'Invoice restored successfully.');
    }

    /**
     * Show the form for creating a new billing record.
     */
    public function create()
    {
        $petOwners = PetOwner::select('pet_owners.*')
            ->join('users', 'pet_owners.user_id', '=', 'users.id')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();
        $pets = Pet::orderBy('name')->get();
        $defaultTaxRate = ClinicSetting::defaultTaxRate();
        
        return view('admin.billing.create', compact('petOwners', 'pets', 'defaultTaxRate'));
    }

    /**
     * Store a newly created billing record in storage.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        $data = $request->validate([
            'pet_id' => 'nullable|exists:pets,id',
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Generate invoice number and sequence
            $prefix = ClinicSetting::invoicePrefix();
            $defaultTaxRate = ClinicSetting::defaultTaxRate();
            $issueDate = $data['invoice_date'];
            $year = date('Y', strtotime($issueDate));
            
            $lastSequence = BillingInvoice::where('invoice_prefix', $prefix)
                ->whereYear('issue_date', $year)
                ->max('invoice_sequence');
            
            $nextSequence = $lastSequence ? $lastSequence + 1 : 1;
            $invoiceNumber = sprintf('%s-%s-%06d', $prefix, $year, $nextSequence);
            
            $invoice = BillingInvoice::create([
                'invoice_number' => $invoiceNumber,
                'invoice_prefix' => $prefix,
                'invoice_sequence' => $nextSequence,
                'pet_id' => $data['pet_id'] ?? null,
                'owner_id' => $data['pet_owner_id'],
                'issue_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'status' => 'pending',
                'tax_rate' => $data['tax_rate'] ?? $defaultTaxRate,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                BillingInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['item_type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            $notificationService->sendToRole(
                'reception',
                Notification::TYPE_PAYMENT,
                'Payment Due',
                'Invoice ' . $invoice->invoice_number . ' is due on ' . $invoice->due_date . '.',
                [
                    'reference_type' => 'invoice',
                    'reference_id' => $invoice->id,
                    'action_url' => route('admin.billing.show', $invoice->id),
                ]
            );
            
            return redirect()->route('admin.billing.index')
                ->with('success', 'Invoice created successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified billing record.
     */
    public function show($id)
    {
        $invoice = BillingInvoice::with([
            'pet', 
            'petOwner', 
            'invoiceItems',
            'payments' => function($query) {
                $query->orderBy('payment_date', 'desc');
            }
        ])->findOrFail($id);
        
        return view('admin.billing.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified billing record.
     */
    public function edit($id)
    {
        $invoice = BillingInvoice::with(['invoiceItems'])->findOrFail($id);
        
        $petOwners = PetOwner::select('pet_owners.*')
            ->join('users', 'pet_owners.user_id', '=', 'users.id')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();
        $pets = Pet::orderBy('name')->get();
        
        return view('admin.billing.edit', compact('invoice', 'petOwners', 'pets'));
    }

    /**
     * Update the specified billing record in storage.
     */
    public function update(Request $request, $id)
    {
        $invoice = BillingInvoice::findOrFail($id);
        $defaultTaxRate = ClinicSetting::defaultTaxRate();
        
        $data = $request->validate([
            'pet_id' => 'nullable|exists:pets,id',
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $invoice->update([
                'pet_id' => $data['pet_id'],
                'owner_id' => $data['pet_owner_id'],
                'issue_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'tax_rate' => $data['tax_rate'] ?? $defaultTaxRate,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->invoiceItems()->delete();

            foreach ($data['items'] as $item) {
                BillingInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['item_type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.billing.show', $invoice->id)
                ->with('success', 'Invoice updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified billing record from storage.
     */
    public function destroy($id)
    {
        $invoice = BillingInvoice::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $invoice->invoiceItems()->delete();
            $invoice->payments()->delete();
            $invoice->delete();
            
            DB::commit();
            
            return redirect()->route('admin.billing.index')
                ->with('success', 'Invoice deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete invoice: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Show payment form.
     */
    public function paymentForm($id)
    {
        $invoice = BillingInvoice::with(['pet', 'petOwner'])->findOrFail($id);
        
        if ($invoice->is_paid) {
            return back()->withErrors(['error' => 'Invoice is already fully paid.']);
        }
        
        return view('admin.billing.payment', compact('invoice'));
    }
    
    /**
     * Process payment.
     */
    public function processPayment(Request $request, $id)
    {
        $invoice = BillingInvoice::findOrFail($id);
        
        if ($invoice->is_paid) {
            return back()->withErrors(['error' => 'Invoice is already fully paid.']);
        }
        
        $data = $request->validate([
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,check,mobile_payment,insurance,other',
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'payment_date' => 'required|date|before_or_equal:today',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            BillingPayment::create([
                'invoice_id' => $invoice->id,
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'reference_number' => $data['transaction_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'received_by' => auth()->id(),
            ]);

            $invoice->load(['invoiceItems', 'payments']);
            if ($invoice->balance <= 0) {
                $invoice->update(['status' => 'paid']);
            } else {
                $invoice->update(['status' => 'partial']);
            }

            $this->notifyCustomerInvoiceStatus(
                $invoice,
                Notification::TYPE_PAYMENT,
                'Invoice Payment Updated',
                'Payment was posted to invoice ' . $invoice->invoice_number . '. Current status: ' . ucfirst((string) $invoice->status) . '.'
            );
            
            DB::commit();
            
            return redirect()->route('admin.billing.show', $invoice->id)
                ->with('success', 'Payment processed successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to process payment: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Generate invoice from appointment.
     */
    public function generateFromAppointment($appointmentId, NotificationService $notificationService)
    {
        $appointment = Appointment::with(['pet'])->findOrFail($appointmentId);
        $petOwnerId = $appointment->pet ? $appointment->pet->owner_id : null;

        $tempNum = 'INV-' . date('YmdHis') . '-' . substr(uniqid(), -4);
        $prefix = ClinicSetting::invoicePrefix();
        $defaultTaxRate = ClinicSetting::defaultTaxRate();

        $invoice = BillingInvoice::create([
            'invoice_number' => $tempNum,
            'appointment_id' => $appointment->id,
            'pet_id' => $appointment->pet_id,
            'owner_id' => $petOwnerId,
            'invoice_prefix' => $prefix,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'pending',
            'tax_rate' => $defaultTaxRate,
            'discount_amount' => 0,
            'notes' => 'Invoice for appointment on ' . $appointment->appointment_date->format('M d, Y'),
        ]);
        $invoice->invoice_number = $invoice->generateInvoiceNumber();
        $invoice->save();

        BillingInvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'consultation',
            'description' => 'Veterinary Consultation',
            'quantity' => 1,
            'unit_price' => 50.00,
        ]);

        $notificationService->sendToRole(
            'reception',
            Notification::TYPE_PAYMENT,
            'Payment Due',
            'Invoice ' . $invoice->invoice_number . ' is due on ' . $invoice->due_date . '.',
            [
                'reference_type' => 'invoice',
                'reference_id' => $invoice->id,
                'action_url' => route('admin.billing.show', $invoice->id),
            ]
        );
        
        return redirect()->route('admin.billing.edit', $invoice->id)
            ->with('success', 'Invoice generated from appointment successfully.');
    }
    
    /**
     * Send invoice to client.
     */
    public function sendInvoice($id)
    {
        $invoice = BillingInvoice::findOrFail($id);
        
        $invoice->status = 'pending';
        $invoice->save();

        $this->notifyCustomerInvoiceStatus(
            $invoice,
            Notification::TYPE_PAYMENT,
            'New Invoice Available',
            'Invoice ' . $invoice->invoice_number . ' is now available and pending payment.'
        );
        
        return redirect()->route('admin.billing.show', $invoice->id)
            ->with('success', 'Invoice sent to client successfully.');
    }
    
    /**
     * Mark invoice as overdue.
     */
    public function markOverdue($id, NotificationService $notificationService)
    {
        $invoice = BillingInvoice::findOrFail($id);
        
        if (!$invoice->is_paid && \Carbon\Carbon::parse($invoice->due_date)->isPast()) {
            $invoice->status = 'overdue';
            $invoice->save();

            $notificationService->sendToRole(
                'reception',
                Notification::TYPE_PAYMENT_OVERDUE,
                'Payment Overdue',
                'Invoice ' . $invoice->invoice_number . ' is now overdue.',
                [
                    'reference_type' => 'invoice',
                    'reference_id' => $invoice->id,
                    'action_url' => route('admin.billing.show', $invoice->id),
                ]
            );

            $this->notifyCustomerInvoiceStatus(
                $invoice,
                Notification::TYPE_PAYMENT_OVERDUE,
                'Invoice Overdue',
                'Invoice ' . $invoice->invoice_number . ' is now overdue. Please settle payment as soon as possible.'
            );
        }
        
        return redirect()->route('admin.billing.show', $invoice->id)
            ->with('success', 'Invoice marked as overdue.');
    }

    private function notifyCustomerInvoiceStatus(BillingInvoice $invoice, string $type, string $title, string $message): void
    {
        $invoice->loadMissing('owner.user');
        $customer = $invoice->owner?->user;

        if (!$customer) {
            return;
        }

        app(NotificationService::class)->send(
            $customer,
            $type,
            $title,
            $message,
            [
                'reference_type' => 'invoice',
                'reference_id' => $invoice->id,
                'action_url' => route('customer.billing.show', $invoice->id),
            ]
        );
    }
}
