<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\BillingInvoice;
use App\Models\BillingInvoiceItem;
use App\Models\BillingPayment;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\Appointment;
use App\Models\MedicationDispensing;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class BillingController extends BaseController
{
    /**
     * Display a listing of billing records.
     */
    public function index()
    {
        $invoices = BillingInvoice::with(['pet', 'petOwner', 'invoiceItems'])
            ->orderBy('invoice_date', 'desc')
            ->paginate(20);
            
        $totalInvoices = BillingInvoice::count();
        $paidInvoices = BillingInvoice::where('status', 'paid')->count();
        $overdueInvoices = BillingInvoice::where('status', 'overdue')->count();
        $totalRevenue = BillingInvoice::sum('paid_amount');
        
        return view('admin.billing.index', compact(
            'invoices',
            'totalInvoices',
            'paidInvoices',
            'overdueInvoices',
            'totalRevenue'
        ));
    }

    /**
     * Show the form for creating a new billing record.
     */
    public function create()
    {
        $petOwners = PetOwner::orderBy('name')->get();
        $pets = Pet::orderBy('name')->get();
        
        return view('admin.billing.create', compact('petOwners', 'pets'));
    }

    /**
     * Store a newly created billing record in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $invoice = BillingInvoice::create([
                'invoice_number' => '',
                'pet_id' => $data['pet_id'],
                'pet_owner_id' => $data['pet_owner_id'],
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'status' => 'draft',
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            
            $invoice->invoice_number = $invoice->generateInvoiceNumber();
            $invoice->save();
            
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                $subtotal += $totalPrice;
                
                BillingInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['item_type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                ]);
            }
            
            $invoice->subtotal = $subtotal;
            $invoice->total_amount = $subtotal + $invoice->tax_amount - $invoice->discount_amount;
            $invoice->save();
            
            DB::commit();
            
            return redirect()->route('admin.billing.show', $invoice->id)
                ->with('success', 'Invoice created successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()]);
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
            'createdBy',
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
        
        if ($invoice->status === 'paid') {
            return back()->withErrors(['error' => 'Cannot edit paid invoice.']);
        }
        
        $petOwners = PetOwner::orderBy('name')->get();
        $pets = Pet::orderBy('name')->get();
        
        return view('admin.billing.edit', compact('invoice', 'petOwners', 'pets'));
    }

    /**
     * Update the specified billing record in storage.
     */
    public function update(Request $request, $id)
    {
        $invoice = BillingInvoice::findOrFail($id);
        
        if ($invoice->status === 'paid') {
            return back()->withErrors(['error' => 'Cannot edit paid invoice.']);
        }
        
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $invoice->update([
                'pet_id' => $data['pet_id'],
                'pet_owner_id' => $data['pet_owner_id'],
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
            ]);
            
            // Remove existing items
            $invoice->invoiceItems()->delete();
            
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                $subtotal += $totalPrice;
                
                BillingInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['item_type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                ]);
            }
            
            $invoice->subtotal = $subtotal;
            $invoice->total_amount = $subtotal + $invoice->tax_amount - $invoice->discount_amount;
            $invoice->save();
            
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
        
        if ($invoice->status === 'paid') {
            return back()->withErrors(['error' => 'Cannot delete paid invoice.']);
        }
        
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
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,check,online_payment',
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'payment_date' => 'required|date|before_or_equal:today',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $payment = BillingPayment::create([
                'invoice_id' => $invoice->id,
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'received_by' => auth()->id(),
            ]);
            
            $invoice->paid_amount += $data['amount'];
            
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'paid';
            } elseif ($invoice->status === 'draft') {
                $invoice->status = 'sent';
            }
            
            $invoice->save();
            
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
    public function generateFromAppointment($appointmentId)
    {
        $appointment = Appointment::with(['pet', 'petOwner'])->findOrFail($appointmentId);
        
        $invoice = BillingInvoice::create([
            'invoice_number' => '',
            'pet_id' => $appointment->pet_id,
            'pet_owner_id' => $appointment->pet_owner_id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'draft',
            'tax_amount' => 0,
            'discount_amount' => 0,
            'notes' => 'Invoice for appointment on ' . $appointment->appointment_date->format('M d, Y'),
            'created_by' => auth()->id(),
        ]);
        
        $invoice->invoice_number = $invoice->generateInvoiceNumber();
        $invoice->save();
        
        // Add consultation fee
        BillingInvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_type' => 'consultation',
            'description' => 'Veterinary Consultation',
            'quantity' => 1,
            'unit_price' => 50.00, // Default consultation fee
            'total_price' => 50.00,
            'billable_type' => Appointment::class,
            'billable_id' => $appointment->id,
        ]);
        
        $invoice->subtotal = 50.00;
        $invoice->total_amount = 50.00;
        $invoice->save();
        
        return redirect()->route('admin.billing.edit', $invoice->id)
            ->with('success', 'Invoice generated from appointment successfully.');
    }
    
    /**
     * Send invoice to client.
     */
    public function sendInvoice($id)
    {
        $invoice = BillingInvoice::findOrFail($id);
        
        $invoice->status = 'sent';
        $invoice->save();
        
        return redirect()->route('admin.billing.show', $invoice->id)
            ->with('success', 'Invoice sent to client successfully.');
    }
    
    /**
     * Mark invoice as overdue.
     */
    public function markOverdue($id)
    {
        $invoice = BillingInvoice::findOrFail($id);
        
        if (!$invoice->is_paid && $invoice->due_date->isPast()) {
            $invoice->status = 'overdue';
            $invoice->save();
        }
        
        return redirect()->route('admin.billing.show', $invoice->id)
            ->with('success', 'Invoice marked as overdue.');
    }
}
